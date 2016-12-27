<?php

namespace SerendipityHQ\Bundle\FeaturesBundle\Service;

use SerendipityHQ\Bundle\FeaturesBundle\Form\DataTransformer\FeaturesCollectionTransformer;
use SerendipityHQ\Bundle\FeaturesBundle\Model\BooleanFeature;
use SerendipityHQ\Bundle\FeaturesBundle\Model\FeatureInterface;
use SerendipityHQ\Bundle\FeaturesBundle\Model\FeaturesCollection;
use SerendipityHQ\Bundle\FeaturesBundle\Model\SubscriptionInterface;
use SerendipityHQ\Bundle\FeaturesBundle\Traits\FeaturesManagerTrait;
use SerendipityHQ\Bundle\FeaturesBundle\Model\FeaturesManagerInterface;
use SerendipityHQ\Bundle\FeaturesBundle\Traits\SubscriptionTrait;
use SerendipityHQ\Component\ValueObjects\Currency\CurrencyInterface;
use SerendipityHQ\Component\ValueObjects\Money\Money;
use SerendipityHQ\Component\ValueObjects\Money\MoneyInterface;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use SerendipityHQ\Bundle\FeaturesBundle\Form\Type\FeaturesType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Contains method to manage features plans.
 */
class FeaturesManager implements FeaturesManagerInterface
{
    use FeaturesManagerTrait;

    /** @var array $differences The added and removed features */
    private $differences;

    /**
     * @param string $subscriptionInterval
     * @param CurrencyInterface $currency
     * @throws \InvalidArgumentException If the $subscriptionInterval does not exist
     *
     * @return array
     */
    public function buildDefaultSubscriptionFeatures(string $subscriptionInterval, CurrencyInterface $currency = null)
    {
        $activeUntil = SubscriptionTrait::calculateActiveUntil($subscriptionInterval);
        $features = [];

        /**
         * @var string $name
         * @var FeatureInterface $details
         */
        foreach ($this->getFeaturesHandler()->getFeatures(FeatureInterface::BOOLEAN) as $name => $details) {
            $features[$name] = [
                'active_until' => false === $this->getFeaturesHandler()->getDefaultStatusForBoolean($name) ? null : $activeUntil,
                'type' => $details->getType(),
                'enabled' => $details->isEnabled()
            ];
        }

        return $features;
    }

    /**
     * @param SubscriptionInterface $subscription
     * @return Money
     */
    public function calculateSubscriptionAmount(SubscriptionInterface $subscription) : MoneyInterface
    {
        $total = new Money(['amount' => 0, 'currency' => $subscription->getCurrency()]);

        /** @var FeatureInterface $feature */
        foreach ($subscription->getFeatures() as $feature)
        {
            if ($feature->isEnabled() && $feature instanceof BooleanFeature) {
                $price = $this->getFeaturesHandler()->getBooleanFeature($feature->getName())->getPrice($subscription->getCurrency(), $subscription->getInterval());
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
                $instantPrice = $this->getFeaturesHandler()->getFeatures()->get($feature)->getInstantPrice($currency, $subscription->getInterval());

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
     * @param array $options
     * @return FormBuilderInterface
     */
    public function getFeaturesFormBuilder(string $actionUrl, SubscriptionInterface $subscription, array $options = [])
    {
        $form = $this->getFormFactory()->createBuilder(FormType::class, [
            'action' => $actionUrl,
            'method' => 'POST',
        ])
            ->add('features', FeaturesType::class, [
                'data' => $subscription->getFeatures()->toArray(),
                'features_handler' => $this->getFeaturesHandler()
            ]);

        $form->get('features')->addModelTransformer(new FeaturesCollectionTransformer());

        return $form;
    }

    /**
     * @param SubscriptionInterface $subscription
     *
     * @return FeaturesManagerInterface
     */
    public function setSubscription(SubscriptionInterface $subscription) : FeaturesManagerInterface
    {
        $this->getFeaturesHandler()->setSubscription($subscription);

        return $this;
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
        $differences = [
            'added'   => [],
            'removed' => [],
        ];

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
                $differences['removed'][] = $oldFeature->getName();
                continue;
            }

            // If it was in the old collection and was enabled and is in the new collection but is not enabled...
            if (true === $oldFeature->isEnabled()
                && true === $newFeatures->containsKey($oldFeature->getName())
                && false === $newFeatures->get($oldFeature->getName())->isEnabled()
            ) {
                // ... It was removed
                $differences['removed'][] = $oldFeature->getName();
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
                $differences['added'][] = $newFeature->getName();
                continue;
            }

            // If it was in the old collection and was not enabled and is in the new collection too but is enabled
            if (true === $newFeature->isEnabled()
                && true === $oldFeatures->containsKey($newFeature->getName())
                && false === $oldFeatures->get($newFeature->getName())->isEnabled()
            ) {
                // ... It was added
                $differences['added'][] = $newFeature->getName();
            }
        }

        $this->differences = $differences;

        return $this->getDifferences();
    }

    private function isStillActive(string $featureName, FeaturesCollection $oldFeatures)
    {
        // If is a feature that was not present in the old plan or, if present, has the activeUntil property === null...
        if (false === $oldFeatures->containsKey($featureName) || null === $oldFeatures->get($featureName)->getActiveUntil()) {
            // ... It is for sure a feature not still active
            return false;
        }

        die(dump($oldFeatures));
    }
}
