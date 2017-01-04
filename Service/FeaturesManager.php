<?php

namespace SerendipityHQ\Bundle\FeaturesBundle\Service;

use SerendipityHQ\Bundle\FeaturesBundle\Form\DataTransformer\FeaturesCollectionTransformer;
use SerendipityHQ\Bundle\FeaturesBundle\Model\ConfiguredBooleanFeatureInterface;
use SerendipityHQ\Bundle\FeaturesBundle\Model\ConfiguredCountableFeatureInterface;
use SerendipityHQ\Bundle\FeaturesBundle\Model\ConfiguredRechargeableFeatureInterface;
use SerendipityHQ\Bundle\FeaturesBundle\Model\FeatureInterface;
use SerendipityHQ\Bundle\FeaturesBundle\Model\ConfiguredFeaturesCollection;
use SerendipityHQ\Bundle\FeaturesBundle\Model\SubscribedBooleanFeatureInterface;
use SerendipityHQ\Bundle\FeaturesBundle\Model\SubscribedFeaturesCollection;
use SerendipityHQ\Bundle\FeaturesBundle\Model\SubscribedRecurringFeatureInterface;
use SerendipityHQ\Bundle\FeaturesBundle\Model\Subscription;
use SerendipityHQ\Bundle\FeaturesBundle\Model\SubscriptionInterface;
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
    /** @var ConfiguredFeaturesCollection $configuredFeatures */
    private $configuredFeatures;

    /** @var FormFactory $formFactory */
    private $formFactory;

    /** @var InvoicesManager $invoicesManager */
    private $invoicesManager;

    /** @var SubscriptionInterface $subscription */
    private $subscription;

    /**
     * @param array $configuredFeatures
     */
    public function __construct(array $configuredFeatures)
    {
        $this->configuredFeatures = new ConfiguredFeaturesCollection($configuredFeatures);
    }

    /** @var array $differences The added and removed features */
    private $differences = [
            'added' => [],
            'removed' => [],
        ];

    /**
     * Returns all the configured features.
     *
     * @return ConfiguredFeaturesCollection
     */
    public function getConfiguredFeatures() : ConfiguredFeaturesCollection
    {
        return $this->configuredFeatures;
    }

    /**
     * @return SubscriptionInterface
     */
    public function getSubscription() : SubscriptionInterface
    {
        return $this->subscription;
    }

    /**
     * @param SubscriptionInterface $subscription
     *
     * @return FeaturesManager
     */
    public function setSubscription(SubscriptionInterface $subscription) : self
    {
        $this->subscription = $subscription;
        $this->getInvoicesManager()->setSubscription($this->getSubscription());

        //$this->configurePricesInSubscriptionFeatures($subscription);

        return $this;
    }

    /**
     * @param string $subscriptionInterval
     *
     * @throws \InvalidArgumentException If the $subscriptionInterval does not exist
     *
     * @return SubscribedFeaturesCollection
     */
    public function buildDefaultSubscriptionFeatures(string $subscriptionInterval) : SubscribedFeaturesCollection
    {
        $activeUntil = Subscription::calculateActiveUntil($subscriptionInterval);
        $features = [];

        /**
         * @var string
         * @var FeatureInterface|ConfiguredBooleanFeatureInterface|ConfiguredCountableFeatureInterface|ConfiguredRechargeableFeatureInterface $details
         */
        foreach ($this->getConfiguredFeatures() as $name => $details) {
            switch ($details->getType()) {
                case 'boolean':
                    /** @var ConfiguredBooleanFeatureInterface $details */
                    $features[$name] = [
                        'active_until' => false === $this->getConfiguredFeatures()->get($name)->isEnabled() ? null : $activeUntil,
                        'type' => $details->getType(),
                        'enabled' => $details->isEnabled(),
                    ];
                    break;
                case 'countable':
                    /** @var ConfiguredCountableFeatureInterface $details */
                    $features[$name] = [
                        'type' => $details->getType()
                    ];
                    break;
                case 'rechargeable':
                    /** @var ConfiguredRechargeableFeatureInterface $details */
                    $features[$name] = [
                        'type' => $details->getType(),
                        'recharge_amount' => $this->getConfiguredFeatures()->get($name)->getFreeRecharge(),
                        'active_until' => $activeUntil
                    ];
                    break;
            }
        }

        return new SubscribedFeaturesCollection($features);
    }

    /**
     * @param SubscribedFeaturesCollection $newFeatures This comes from the form, not from the Subscription! The Subscription is
     *                                        not yet synced with these new Features!
     *
     * @return Money
     */
    public function calculateTotalChargesForNewFeatures(SubscribedFeaturesCollection $newFeatures)
    {
        $totalCharges = new Money(['amount' => 0, 'currency' => $this->getSubscription()->getCurrency()]);

        // Calculate the added and removed features
        $this->findDifferences($this->getSubscription()->getFeatures(), $newFeatures);

        /*
         * May happen that a premium feature is activate and paid, then is deactivated but it is still in the subscription interval.
         * If it is activated again during the subscription interval, it were already paid, so it hasn't to be paid again.
         */
        foreach ($this->getDifferences('added') as $feature) {
            $checkingFeature = $this->getSubscription()->getFeatures()->get($feature);
            if (null !== $checkingFeature && false === $checkingFeature->isStillActive()) {
                $instantPrice = $this->getConfiguredFeatures()->get($feature)->getInstantPrice($this->getSubscription()->getCurrency(), $this->getSubscription()->getInterval());

                if ($instantPrice instanceof MoneyInterface) {
                    $totalCharges = $totalCharges->add($instantPrice);
                }
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
     * @param string                $actionUrl
     * @param SubscriptionInterface $subscription
     *
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
                'configured_features' => $this->getConfiguredFeatures()->setSubscription($subscription),
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
     * @return ConfiguredFeaturesCollection
     *
     * @todo Method to implement
     */
    public function getPremiumFeaturesReview()
    {
        return $this->getConfiguredFeatures();
    }

    /**
     * @param SubscriptionInterface $subscription
     * @param SubscribedFeaturesCollection    $features
     */
    public function syncSubscription(SubscriptionInterface $subscription, SubscribedFeaturesCollection $features)
    {
        foreach ($features as $featureName => $feature) {
            $toggle = $feature->isEnabled() ? 'enable' : 'disable';
            $subscription->getFeatures()->get($featureName)->$toggle();
        }
    }

    /**
     * Update the subscription object after features are added or removed.
     *
     * It updates the next payment amount and the dates untile the features are active.
     *
     * If a ConfiguredFeaturesCollection is passed, it sets their statuses to the features already existent in the Subscription.
     *
     * @param SubscribedFeaturesCollection|null $newFeatures
     */
    public function updateSubscription(SubscribedFeaturesCollection $newFeatures = null)
    {
        /**
         * Before all, update the features, setting the new enabled status or adding the feature if not already present.
         *
         * @var FeatureInterface
         */
        foreach ($newFeatures as $newFeature) {
            $existentFeature = $this->getSubscription()->getFeatures()->get($newFeature->getName());

            if ($existentFeature instanceof FeatureInterface) {
                $toggle = $newFeature->isEnabled() ? 'enable' : 'disable';
                $existentFeature->$toggle();
            }

            if (false === $this->getSubscription()->has($newFeature->getName())) {
                $this->getSubscription()->addFeature($newFeature->getName(), $newFeature);
            }
        }

        $this->updateNextPaymentAmount();
        $this->updateUntilDates();
    }

    /**
     * @return MoneyInterface
     */
    private function calculateSubscriptionAmount() : MoneyInterface
    {
        $total = new Money(['amount' => 0, 'currency' => $this->getSubscription()->getCurrency()]);

        /** @var FeatureInterface $feature */
        foreach ($this->getSubscription()->getFeatures() as $feature) {
            if ($feature instanceof SubscribedBooleanFeatureInterface && $feature->isEnabled()) {
                $price = $this->getConfiguredFeatures()->get($feature->getName())->getPrice($this->getSubscription()->getCurrency(), $this->getSubscription()->getInterval());

                if ($price instanceof MoneyInterface) {
                    $total = $total->add($price);
                }
            }
        }

        return $total;
    }

    /**
     * Calculate differences between two FeaturesCollections.
     *
     * Calculates the added and removed features in the $newFeatures comparing it with $oldFeatures
     *
     * @param SubscribedFeaturesCollection $oldFeatures
     * @param SubscribedFeaturesCollection $newFeatures
     *
     * @return array
     */
    private function findDifferences(SubscribedFeaturesCollection $oldFeatures, SubscribedFeaturesCollection $newFeatures)
    {
        /**
         * Calculate the removed features.
         *
         * A feature is removed if:
         * 1. It was in the old collection but doesn't exist in the new collection;
         * 2. It was in the old collection and was enabled and is in the new collection but is not enabled
         *
         * @var FeatureInterface
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
         * @var FeatureInterface
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
     * Updates the amount of the next payment for the provided subscription object.
     */
    private function updateNextPaymentAmount()
    {
        $this->getSubscription()->setNextPaymentAmount($this->calculateSubscriptionAmount());
    }

    /**
     * Updates the date until the features in the Subscription are active.
     */
    private function updateUntilDates()
    {
        $validUntil = $this->getSubscription()->getNextPaymentOn();

        /** @var string $feature */
        foreach ($this->getDifferences('added') as $feature) {
            if (false === $this->getSubscription()->has($feature)) {
                $this->getSubscription()->addFeature(
                    $feature, $this->getConfiguredFeatures()->get($feature)
                );
            }

            /** @var FeatureInterface $updatingFeature */
            $updatingFeature = $this->getSubscription()->getFeatures()->get($feature);

            if ($updatingFeature instanceof SubscribedRecurringFeatureInterface)
                $updatingFeature->setActiveUntil($validUntil);
        }
    }

    /**
     * Calculates the bitmask of the boolean features selected
     * by the merchant and returns the corresponding value in binary format.
     *
     * @param  $features
     * @param array $options
     *
     * @return int
     */
    /*
    protected function calculatePlanId(PremiumStoreEmbeddable $features, array $options)
    {
        // No feature is selected
        $booleanBitmask = 0;
        $amount = 0;

        if ($features->hasAds()) {
            $amount += $this->plans['boolean']['ads']['price'][$options['currency']][$options['interval']];
            $booleanBitmask += PremiumStoreEmbeddable::ADS;
        }

        if ($features->hasSeo()) {
            $amount += $this->plans['boolean']['seo']['price'][$options['currency']][$options['interval']];
            $booleanBitmask += PremiumStoreEmbeddable::SEO;
        }

        if ($features->hasSocial()) {
            $amount += $this->plans['boolean']['social']['price'][$options['currency']][$options['interval']];
            $booleanBitmask += PremiumStoreEmbeddable::SOCIAL;
        }

        $booleanBitmask = decbin($booleanBitmask);

        return
            $booleanBitmask
            . '_' . $options['interval']
            . '_' . $options['trial_period_days']
            . '_' . $options['currency']
            . '_' . $amount;
    }
    */

    /**
     * Sets the general configurations (as prices, for examples) in the FeatureINterface objects loaded from a
     * SubscriptionInterface object.
     *
     * @param SubscriptionInterface $subscription
     */
    /*
    private function configurePricesInSubscriptionFeatures(SubscriptionInterface $subscription)
    {
        /** @var FeatureInterface $feature *
        foreach ($subscription->getFeatures() as $feature) {
            $prices = $this->getConfiguredFeatures()->get($feature->getName())->getPrices();
            $feature->setPrices($prices);
        }
    }
    */

    /**
     * @return FormFactory
     */
    public function getFormFactory() : FormFactory
    {
        return $this->formFactory;
    }

    /**
     * @return InvoicesManager
     */
    public function getInvoicesManager() : InvoicesManager
    {
        return $this->invoicesManager;
    }

    /**
     * @param FormFactory $formFactory
     */
    public function setFormFactory(FormFactory $formFactory)
    {
        $this->formFactory = $formFactory;
    }

    /**
     * @param InvoicesManager $invoicesManager
     */
    public function setInvoicesManager(InvoicesManager $invoicesManager)
    {
        $this->invoicesManager = $invoicesManager;
    }
}
