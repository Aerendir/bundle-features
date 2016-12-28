<?php

namespace SerendipityHQ\Bundle\FeaturesBundle\Service;

use SerendipityHQ\Bundle\FeaturesBundle\Form\DataTransformer\FeaturesCollectionTransformer;
use SerendipityHQ\Bundle\FeaturesBundle\Model\BooleanFeature;
use SerendipityHQ\Bundle\FeaturesBundle\Model\FeatureInterface;
use SerendipityHQ\Bundle\FeaturesBundle\Model\FeaturesCollection;
use SerendipityHQ\Bundle\FeaturesBundle\Model\Subscription;
use SerendipityHQ\Bundle\FeaturesBundle\Model\SubscriptionInterface;
use SerendipityHQ\Component\ValueObjects\Currency\CurrencyInterface;
use SerendipityHQ\Component\ValueObjects\Money\Money;
use SerendipityHQ\Component\ValueObjects\Money\MoneyInterface;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use SerendipityHQ\Bundle\FeaturesBundle\Form\Type\FeaturesType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormFactory;

/**
 * Contains method to manage features plans.
 */
class FeaturesManager
{
    /** @var  FeaturesCollection $configuredFeatures */
    private $configuredFeatures;

    /** @var FormFactory $formFactory */
    private $formFactory;

    /** @var  SubscriptionInterface $subscription */
    private $subscription;

    /**
     * @param array $configuredFeatures
     */
    public function __construct(array $configuredFeatures)
    {
        $this->configuredFeatures = new FeaturesCollection($configuredFeatures);
    }

    /** @var array $differences The added and removed features */
    private $differences = [
            'added'   => [],
            'removed' => [],
        ];

    /**
     * @param string $subscriptionInterval
     * @throws \InvalidArgumentException If the $subscriptionInterval does not exist
     *
     * @return array
     */
    public function buildDefaultSubscriptionFeatures(string $subscriptionInterval)
    {
        $activeUntil = Subscription::calculateActiveUntil($subscriptionInterval);
        $features = [];

        /**
         * @var string $name
         * @var FeatureInterface $details
         */
        foreach ($this->getConfiguredFeatures() as $name => $details) {
            $features[$name] = [
                'active_until' => false === $this->getConfiguredFeatures()->get($name) ? null : $activeUntil,
                'type' => $details->getType(),
                'enabled' => $details->isEnabled()
            ];
        }

        return $features;
    }

    /**
     * @param SubscriptionInterface $subscription
     * @return MoneyInterface
     */
    public function calculateSubscriptionAmount(SubscriptionInterface $subscription) : MoneyInterface
    {
        $total = new Money(['amount' => 0, 'currency' => $subscription->getCurrency()]);

        /** @var FeatureInterface $feature */
        foreach ($subscription->getFeatures() as $feature)
        {
            if ($feature->isEnabled() && $feature instanceof BooleanFeature) {
                $price = $this->getConfiguredFeatures()->get($feature->getName())->getPrice($subscription->getCurrency(), $subscription->getInterval());
                $total = $total->add($price);
            }
        }

        return $total;
    }

    /**
     * @param CurrencyInterface $currency
     * @param SubscriptionInterface $subscription
     * @param FeaturesCollection $newFeatures
     * @return Money
     */
    public function calculateTotalChargesForNewFeatures(CurrencyInterface $currency, SubscriptionInterface $subscription, FeaturesCollection $newFeatures)
    {
        $totalCharges = new Money(['amount' => 0, 'currency' => $currency]);

        // Calculate the added and removed features
        $this->findDifferences($subscription->getFeatures(), $newFeatures);

        /*
         * May happen that a premium feature is activate and paid, then is deactivated but it is still in the subscription interval.
         * If it is activated again during the subscription interval, it were already paid, so it hasn't to be paid again.
         */
        foreach ($this->getDifferences('added') as $feature) {
            if (false === $this->isStillActive($feature, $subscription->getFeatures())) {
                $instantPrice = $this->getConfiguredFeatures()->get($feature)->getInstantPrice($currency, $subscription->getInterval());

                $totalCharges = $totalCharges->add($instantPrice);
            }
        }

        return $totalCharges;
    }

    /**
     * @param string $type
     *
     * @return array
     */
    public function getDifferences($type = null)
    {
        if (null === $this->differences) {
            throw new \LogicException('No differences calculated. You have to first call findDifferences().');
        }

        if ('added' !== $type && 'removed' !== $type && null !== $type) {
            throw new \InvalidArgumentException(sprintf('You can only get "added" or "removed" differences or all passing "null". You asked for "%s".', $type));
        }

        return null === $type ? $this->differences : $this->differences[$type];
    }

    /**
     * @param string $actionUrl
     * @param SubscriptionInterface $subscription
     * @return FormBuilderInterface
     */
    public function getFeaturesFormBuilder(string $actionUrl, SubscriptionInterface $subscription)
    {
        $form = $this->getFormFactory()->createBuilder(FormType::class, [
            'action' => $actionUrl,
            'method' => 'POST',
        ])
            ->add('features', FeaturesType::class, [
                'data' => $subscription->getFeatures()->toArray(),
                'configured_features' => $this->getConfiguredFeatures()
            ]);

        $form->get('features')->addModelTransformer(new FeaturesCollectionTransformer());

        return $form;
    }

    /**
     * Returns the premium features activated and not activated.
     *
     * Given a Subscription object, intersect it with configured features.
     * Of configured features, this takes care only of the Premium ones (the ones that has at least one price set).
     * So, if a Premium configured feature is not present in the given Subscription, it is set as not enabled, while, if
     * it exists in the given Subscription, it has its same status (if enabled in the Subscription, it will be enabled,
     * disabled instead).
     *
     * @return FeaturesCollection
     *
     * @todo Method to implement.
     */
    public function getPremiumFeaturesReview()
    {
        return $this->getConfiguredFeatures();
    }

    /**
     * @param SubscriptionInterface $subscription
     * @param FeaturesCollection $features
     */
    public function syncSubscription(SubscriptionInterface $subscription, FeaturesCollection $features)
    {
        foreach ($features as $featureName => $feature)
        {
            $toggle = $feature->isEnabled() ? 'enable' : 'disable';
            $subscription->getFeatures()->get($featureName)->$toggle();
        }
    }

    /**
     * @param SubscriptionInterface $subscription
     */
    public function updateUntilDates(SubscriptionInterface $subscription)
    {
        $validUntil = $subscription->getNextPaymentOn();

        /** @var string $feature */
        foreach ($this->getDifferences('added') as $feature) {
            if (false === $subscription->has($feature)) {
                $subscription->addFeature(
                    $feature, $this->getConfiguredFeatures()->get($feature)
                );
            }

            /** @var FeatureInterface $updatingFeature */
            $updatingFeature = $subscription->getFeatures()->get($feature);
            $updatingFeature->setActiveUntil($validUntil);
        }
    }

    /**
     * Calculate differences between two FeaturesCollections.
     *
     * Calculates the added and removed features in the $newFeatures comparing it with $oldFeatures
     *
     * @param FeaturesCollection $oldFeatures
     * @param FeaturesCollection $newFeatures
     *
     * @return array
     */
    private function findDifferences(FeaturesCollection $oldFeatures, FeaturesCollection $newFeatures)
    {
        /**
         * Calculate the removed features.
         *
         * A feature is removed if:
         * 1. It was in the old collection but doesn't exist in the new collection;
         * 2. It was in the old collection and was enabled and is in the new collection but is not enabled
         *
         * @var FeatureInterface $oldFeatures
         */
        foreach ($oldFeatures as $oldFeature) {
            // If the feature is in the old collection but doesn't exist in the new collection...
            if (false === $newFeatures->containsKey($oldFeature->getName())) {
                // ... It was removed
                $this->differences['removed'][] = $oldFeature->getName();
                continue;
            }

            // If it was in the old collection and was enabled and is in the new collection but is not enabled...
            if (true === $oldFeature->isEnabled()
                && true === $newFeatures->containsKey($oldFeature->getName())
                && false === $newFeatures->get($oldFeature->getName())->isEnabled()
            ) {
                // ... It was removed
                $this->differences['removed'][] = $oldFeature->getName();
            }
        }

        /**
         * Calculate the added features.
         *
         * A feature is added if:
         * 1. It was not in the old collection but exists in the new collection;
         * 2. It was in the old collection and was not enabled and is in the new collection too but is enabled
         *
         * @var FeatureInterface $newFeatures
         */
        foreach ($newFeatures as $newFeature) {
            // If the feature was not in the old collection but exists in the new collection...
            if (false === $oldFeatures->containsKey($newFeature->getName())) {
                // ... It was added
                $this->differences['added'][] = $newFeature->getName();
                continue;
            }

            // If it was in the old collection and was not enabled and is in the new collection too but is enabled
            if (true === $newFeature->isEnabled()
                && true === $oldFeatures->containsKey($newFeature->getName())
                && false === $oldFeatures->get($newFeature->getName())->isEnabled()
            ) {
                // ... It was added
                $this->differences['added'][] = $newFeature->getName();
            }
        }

        return $this->getDifferences();
    }

    /**
     * @param string $featureName
     * @param FeaturesCollection $oldFeatures
     * @return bool
     */
    private function isStillActive(string $featureName, FeaturesCollection $oldFeatures)
    {
        // If is a feature that was not present in the old plan or, if present, has the activeUntil property === null...
        if (false === $oldFeatures->containsKey($featureName) || null === $oldFeatures->get($featureName)->getActiveUntil()) {
            // ... It is for sure a feature not still active
            return false;
        }

        throw new \RuntimeException('Complete the implementation');
    }

    /**
     * Returns all the configured features.
     *
     * @return FeaturesCollection
     */
    public function getConfiguredFeatures() : FeaturesCollection
    {
        return $this->configuredFeatures;
    }

    /**
     * @return FormFactory
     */
    public function getFormFactory() : FormFactory
    {
        return $this->formFactory;
    }

    /**
     * @return SubscriptionInterface
     */
    public function getSubscription() : SubscriptionInterface
    {
        return $this->subscription;
    }

    /**
     * @param FormFactory $formFactory
     */
    public function setFormFactory(FormFactory $formFactory)
    {
        $this->formFactory = $formFactory;
    }

    /**
     * @param SubscriptionInterface $subscription
     *
     * @return FeaturesManager
     */
    public function setSubscription(SubscriptionInterface $subscription) : self
    {
        $this->subscription = $subscription;

        return $this;
    }
}
