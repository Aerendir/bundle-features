<?php

declare(strict_types=1);

/*
 * This file is part of the Serendipity HQ Features Bundle.
 *
 * Copyright (c) Adamo Aerendir Crespi <aerendir@serendipityhq.com>.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SerendipityHQ\Bundle\FeaturesBundle\Manager;

use SerendipityHQ\Bundle\FeaturesBundle\Form\DataTransformer\FeaturesCollectionTransformer;
use SerendipityHQ\Bundle\FeaturesBundle\Form\Type\FeaturesType;
use SerendipityHQ\Bundle\FeaturesBundle\Model\Feature\Configured\ConfiguredBooleanFeature;
use SerendipityHQ\Bundle\FeaturesBundle\Model\Feature\Configured\ConfiguredCountableFeature;
use SerendipityHQ\Bundle\FeaturesBundle\Model\Feature\Configured\ConfiguredFeaturesCollection;
use SerendipityHQ\Bundle\FeaturesBundle\Model\Feature\Configured\ConfiguredRechargeableFeature;
use SerendipityHQ\Bundle\FeaturesBundle\Model\Feature\FeatureInterface;
use SerendipityHQ\Bundle\FeaturesBundle\Model\Feature\Property\CanBeConsumedInterface;
use SerendipityHQ\Bundle\FeaturesBundle\Model\Feature\Property\CanBeEnabledInterface;
use SerendipityHQ\Bundle\FeaturesBundle\Model\Feature\Property\IsRecurringFeatureInterface;
use SerendipityHQ\Bundle\FeaturesBundle\Model\Feature\Subscribed\SubscribedBooleanFeature;
use SerendipityHQ\Bundle\FeaturesBundle\Model\Feature\Subscribed\SubscribedCountableFeature;
use SerendipityHQ\Bundle\FeaturesBundle\Model\Feature\Subscribed\SubscribedCountableFeaturePack;
use SerendipityHQ\Bundle\FeaturesBundle\Model\Feature\Subscribed\SubscribedFeaturesCollection;
use SerendipityHQ\Bundle\FeaturesBundle\Model\Feature\Subscribed\SubscribedRechargeableFeature;
use SerendipityHQ\Bundle\FeaturesBundle\Model\Subscription;
use SerendipityHQ\Bundle\FeaturesBundle\Model\SubscriptionInterface;
use SerendipityHQ\Component\ValueObjects\Money\Money;
use SerendipityHQ\Component\ValueObjects\Money\MoneyInterface;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormFactoryInterface;

use function Safe\sprintf;

/**
 * Contains method to manage features plans.
 */
final class FeaturesManager
{
    private const ADDED   = 'added';
    private const REMOVED = 'removed';

    /** @var int[] */
    private const INTERVALS = [
        SubscriptionInterface::DAILY    => 0,
        SubscriptionInterface::WEEKLY   => 1,
        SubscriptionInterface::BIWEEKLY => 2,
        SubscriptionInterface::MONTHLY  => 3,
        SubscriptionInterface::YEARLY   => 4,
    ];

    private ConfiguredFeaturesCollection $configuredFeatures;
    private FormFactoryInterface $formFactory;
    private InvoicesManager $invoicesManager;
    private SubscriptionInterface $subscription;
    private SubscriptionInterface $oldSubscription;

    /** @var array $differences The added and removed features */
    private $differences = [
        self::ADDED   => [],
        self::REMOVED => [],
    ];

    public function __construct(array $configuredFeatures, InvoicesManager $invoicesManager, FormFactoryInterface $formFactory)
    {
        $this->configuredFeatures = new ConfiguredFeaturesCollection($configuredFeatures);
        $this->formFactory        = $formFactory;
        $this->invoicesManager    = $invoicesManager;
    }

    /**
     * Returns all the configured features.
     */
    public function getConfiguredFeatures(): ConfiguredFeaturesCollection
    {
        return $this->configuredFeatures;
    }

    public function getSubscription(): SubscriptionInterface
    {
        return $this->subscription;
    }

    public function setSubscription(SubscriptionInterface $subscription): self
    {
        $this->subscription = $subscription;
        $this->getInvoicesManager()->setSubscription($this->getSubscription());
        $this->getConfiguredFeatures()->setSubscription($this->getSubscription());

        /**
         * Set the Configured feature in each subscribed feature.
         */
        foreach ($subscription->getFeatures()->getValues() as $subscribedFeature) {
            $configuredFeature = $this->getConfiguredFeatures()->get($subscribedFeature->getName());

            // If the feature doesn't exist anymore in configuration, skip it: it will be deleted on next subscription update
            if (null !== $configuredFeature) {
                $subscribedFeature->setConfiguredFeature($configuredFeature);
            }
        }

        return $this;
    }

    public function setTax(float $rate, string $name): self
    {
        $this->getConfiguredFeatures()->setTax($rate, $name);
        $this->getInvoicesManager()->getConfiguredFeatures()->setTax($rate, $name);

        return $this;
    }

    public function buildDefaultSubscriptionFeatures(string $subscriptionInterval): SubscribedFeaturesCollection
    {
        $activeUntil = Subscription::calculateActiveUntil($subscriptionInterval);
        $features    = [];

        foreach ($this->getConfiguredFeatures() as $name => $details) {
            switch ($details->getType()) {
                case FeatureInterface::TYPE_BOOLEAN:
                    /** @var ConfiguredBooleanFeature $details */
                    $features[$name] = [
                        FeatureInterface::FIELD_TYPE                    => $details->getType(),
                        CanBeEnabledInterface::FIELD_ENABLED            => $details->isEnabled(),
                        IsRecurringFeatureInterface::FIELD_ACTIVE_UNTIL => false === $this->getConfiguredFeatures()->get($name)->isEnabled() ? null : $activeUntil,
                    ];

                    break;
                case FeatureInterface::TYPE_COUNTABLE:
                    /** @var ConfiguredCountableFeature $details */
                    $features[$name] = [
                        FeatureInterface::FIELD_TYPE                      => $details->getType(),
                        SubscribedCountableFeature::FIELD_SUBSCRIBED_PACK => [SubscribedCountableFeature::FIELD_SUBSCRIBED_NUM_OF_UNITS => $this->getConfiguredFeatures()->get($name)->getFreePack()->getNumOfUnits()],
                        CanBeConsumedInterface::REMAINED_QUANTITY         => $this->getConfiguredFeatures()->get($name)->getFreePack()->getNumOfUnits(),
                    ];

                    break;
                case FeatureInterface::TYPE_RECHARGEABLE:
                    /** @var ConfiguredRechargeableFeature $details */
                    $features[$name] = [
                        FeatureInterface::FIELD_TYPE              => $details->getType(),
                        'last_recharge_on'                        => new \DateTime(),
                        'last_recharge_quantity'                  => $this->getConfiguredFeatures()->get($name)->getFreeRecharge(),
                        CanBeConsumedInterface::REMAINED_QUANTITY => $this->getConfiguredFeatures()->get($name)->getFreeRecharge(),
                    ];

                    break;
            }
        }

        return new SubscribedFeaturesCollection($features);
    }

    /**
     * @param SubscribedFeaturesCollection $newFeatures This comes from the form, not from the Subscription! The Subscription is
     *                                                  not yet synced with these new Features!
     */
    public function calculateTotalChargesForNewFeatures(SubscribedFeaturesCollection $newFeatures): MoneyInterface
    {
        $totalCharges = new Money([MoneyInterface::BASE_AMOUNT => 0, MoneyInterface::CURRENCY => $this->getSubscription()->getCurrency()]);

        // Calculate the added and removed Boolean features and the changed packages in Countable features
        $this->findDifferences($newFeatures);

        /*
         * May happen that a premium feature is activate and paid, then is deactivated but it is still in the subscription interval.
         * If it is activated again during the subscription interval, it were already paid, so it hasn't to be paid again.
         */
        foreach ($this->getDifferences(self::ADDED) as $feature) {
            $featureName = \is_array($feature) ? \key($feature) : $feature;
            /** @var SubscribedBooleanFeature|SubscribedCountableFeature|SubscribedRechargeableFeature $checkingFeature */
            $checkingFeature = $this->getSubscription()->getFeatures()->get($featureName);

            if (null !== $checkingFeature) {
                /** @var ConfiguredBooleanFeature|ConfiguredCountableFeature|ConfiguredRechargeableFeature $configuredFeature */
                $configuredFeature = $this->getConfiguredFeatures()->get($featureName);
                $price             = null;

                switch (\get_class($checkingFeature)) {
                    // These two have recurring features, so they can or cannot be still active
                    case SubscribedBooleanFeature::class:
                        if (true === $checkingFeature->isStillActive()) {
                            // If it is still active, we have to charge nothing, so continue processing next feature
                            // continue, // Here there was a continue, but Rector reports a PHP warning
                            // PHP Warning:  "continue" targeting switch is equivalent to "break". Did you mean to use "continue 2"?
                            // When you read this comment, evaluate if the bundle continues to work as expected or not.
                            // If it continues to work as expected, remove this entire comment.
                            break;
                        }

                        $price = $configuredFeature->getInstantPrice($this->getSubscription()->getCurrency(), $this->getSubscription()->getRenewInterval(), FeatureInterface::PRICE_GROSS);

                        break;
                    case SubscribedCountableFeature::class:
                        // @todo Support unitary_prices for CountableFeatures https://github.com/Aerendir/bundle-features/issues/1
                        if ($configuredFeature instanceof ConfiguredCountableFeature) {
                            /**
                             * For the moment force the code to get the pack's instant price.
                             *
                             * @var SubscribedCountableFeature $price
                             */
                            $price = $configuredFeature->getPack($checkingFeature->getSubscribedPack()->getNumOfUnits())->getInstantPrice($this->getSubscription()->getCurrency(), $this->getSubscription()->getRenewInterval(), FeatureInterface::PRICE_GROSS);
                        }

                        break;
                        // A RechargeableFeature hasn't a subscription period, so it hasn't an isStillActive() method
                    case SubscribedRechargeableFeature::class:
                        /**
                         * For the moment force the code to get the pack's instant price.
                         */
                        $price = $configuredFeature->getPack($checkingFeature->getRechargingPack()->getNumOfUnits())->getPrice($this->getSubscription()->getCurrency(), FeatureInterface::PRICE_GROSS);

                        break;
                }

                if ($price instanceof MoneyInterface) {
                    $totalCharges = $totalCharges->add($price);
                }
            }
        }

        return $totalCharges;
    }

    public function getDifferences(string $type = null): array
    {
        if (null === $this->differences) {
            throw new \LogicException('No differences calculated. You have to first call findDifferences().');
        }

        if (self::ADDED !== $type && self::REMOVED !== $type && null !== $type) {
            throw new \InvalidArgumentException(sprintf('You can only get "added" or "removed" differences or all passing "null". You asked for "%s".', $type));
        }

        return null === $type ? $this->differences : $this->differences[$type];
    }

    public function getFeaturesFormBuilder(string $actionUrl, SubscriptionInterface $subscription): FormBuilderInterface
    {
        // Generate this form only once
        static $form = null;

        if (null === $form) {
            // Set the subscription in the manager if not already done outside of the bundle by the implementing app
            // Here we assume this features manager is used ever with the same subscription
            if (null === $this->subscription) {
                $this->setSubscription($subscription);
            }

            if (false === $this->getConfiguredFeatures()->isTaxSet()) {
                throw new \RuntimeException("To generate a valid form you have to set a Tax. Call first setTax() and then you'll can call getFeaturesFormBuilder(). Ex.: FeaturesManager::setTax()->getFeaturesFormBuilder()");
            }

            // Clone the $subscription so we can use it to compare changes
            $this->oldSubscription = clone $subscription;

            $form = $this->formFactory->createBuilder(FormType::class, [
                'action' => $actionUrl,
                'method' => 'POST',
            ])
                ->add('features', FeaturesType::class, [
                    'data'                => $subscription->getFeatures()->toArray(),
                    'configured_features' => $this->getConfiguredFeatures(),
                    'subscription'        => $subscription,
                ]);

            $form->get('features')->addModelTransformer(new FeaturesCollectionTransformer());
        }

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
     * @todo Method to implement
     */
    public function getPremiumFeaturesReview(): ConfiguredFeaturesCollection
    {
        return $this->getConfiguredFeatures();
    }

    /**
     * Reverts the Subscription to the state before the editings.
     */
    public function rollbackSubscription(): void
    {
        $this->subscription
            ->setCurrency($this->oldSubscription->getCurrency())
            ->setFeatures($this->oldSubscription->getFeatures())
            ->setRenewInterval($this->oldSubscription->getRenewInterval())
            ->setNextRenewAmount($this->oldSubscription->getNextRenewAmount())
            ->setNextRenewOn($this->oldSubscription->getNextRenewOn());
    }

    public function syncSubscription(SubscriptionInterface $subscription, SubscribedFeaturesCollection $features): void
    {
        foreach ($features as $featureName => $feature) {
            $toggle = $feature->isEnabled() ? 'enable' : 'disable';
            $subscription->getFeatures()->get($featureName)->$toggle();
        }
    }

    /**
     * Update the subscription object after features are added or removed.
     *
     * It updates the next payment amount and the dates until the features are active.
     */
    public function updateSubscription(SubscribedFeaturesCollection $newFeatures = null): void
    {
        if (null !== $newFeatures) {
            /**
             * Before all, update the features, setting the new enabled status or adding the feature if not already present.
             */
            foreach ($newFeatures as $newFeature) {
                $existentFeature = $this->getSubscription()->getFeatures()->get($newFeature->getName());

                // @todo Is this required? Didn't the form already updated the Subscription object? In fact I have an oldSubscription
                if ($existentFeature instanceof SubscribedBooleanFeature) {
                    $toggle = $newFeature->isEnabled() ? 'enable' : 'disable';
                    $existentFeature->$toggle();
                }

                if (false === $this->getSubscription()->has($newFeature->getName())) {
                    $this->getSubscription()->addFeature($newFeature->getName(), $newFeature);
                }
            }
        }

        $this->updateNextPaymentAmount();
        $this->refreshCountableFeatures();
        $this->updateUntilDates();
    }

    /**
     * Renews the countable features at the end of the renew period.
     */
    public function refreshSubscription(): void
    {
        $subscription = $this->getSubscription();

        /** @var FeatureInterface $feature */
        foreach ($subscription->getFeatures()->getValues() as $feature) {
            // If this is not a Countable Feature...
            if ( ! $feature instanceof SubscribedCountableFeature) {
                // Simply continue as it hasn't be renew
                continue;
            }

            /** @var ConfiguredCountableFeature $configuredRenewingFeature Get the configured feature * */
            $configuredRenewingFeature = $this->getConfiguredFeatures()->get($feature->getName());

            // If the feature doesn't exist anymore in the configuration (as it were removed by the developer)
            if (null === $configuredRenewingFeature) {
                // Remove it from the Subscription too
                $subscription->getFeatures()->removeElement($feature);

                // And continue with the next feature
                continue;
            }

            /** @var SubscribedCountableFeature $feature Refresh the feature if the refresh period is elapsed * */
            if ($feature->isRefreshPeriodElapsed()) {
                $feature->refresh();
            }
        }

        $this->refreshCountableFeatures();

        $subscription->forceFeaturesUpdate();
    }

    public function getInvoicesManager(): InvoicesManager
    {
        return $this->invoicesManager;
    }

    private function calculateSubscriptionAmount(): MoneyInterface
    {
        $total = new Money([MoneyInterface::BASE_AMOUNT => 0, MoneyInterface::CURRENCY => $this->getSubscription()->getCurrency()]);

        /** @var FeatureInterface $feature */
        foreach ($this->getSubscription()->getFeatures() as $feature) {
            // Check if the feature is still present in configuration
            if (null === $this->getConfiguredFeatures()->get($feature->getName())) {
                // It is not present anymore: remove it from the subscription
                $this->getSubscription()->getFeatures()->remove($feature->getName());

                // Simply Continue the cycle
                continue;
            }

            if ($feature instanceof SubscribedBooleanFeature && $feature->isEnabled()) {
                $price = $this->getConfiguredFeatures()->get($feature->getName())->getPrice($this->getSubscription()->getCurrency(), $this->getSubscription()->getRenewInterval());

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
     * @return array
     */
    private function findDifferences(SubscribedFeaturesCollection $newFeatures)
    {
        // Get the features from the cloned subscription
        $oldFeatures = $this->oldSubscription->getFeatures();

        /**
         * Calculate the removed features.
         *
         * A feature is removed if:
         * 1. It was in the old collection but doesn't exist in the new collection;
         * 2. It was in the old collection and was enabled and is in the new collection but is not enabled
         */
        foreach ($oldFeatures as $oldFeature) {
            // If the Feature is in the old collection but doesn't exist in the new collection...
            if (false === $newFeatures->containsKey($oldFeature->getName())) {
                // ... It was removed and in this case we can simply set it as removed as we don't need much details
                $this->differences[self::REMOVED][] = $oldFeature->getName();

                continue;
            }

            switch (\get_class($oldFeature)) {
                // If is a BooleanFeature...
                case SubscribedBooleanFeature::class:
                    /** @var SubscribedBooleanFeature $oldFeature */
                    // ... and was in the old collection and was enabled and is in the new collection but is not enabled...
                    if (true === $oldFeature->isEnabled()
                        && true === $newFeatures->containsKey($oldFeature->getName())
                        && false === $newFeatures->get($oldFeature->getName())->isEnabled()
                    ) {
                        // ... It was removed
                        $this->differences[self::REMOVED][] = $oldFeature->getName();
                    }

                    break;
                    // If is a CountableFeature...
                case SubscribedCountableFeature::class:
                    /**
                     * ... and was in the old collection and in the new collection, too ...
                     */
                    if (true === $newFeatures->containsKey($oldFeature->getName())) {
                        /**
                         * We first get the subscribed packages...
                         */
                        $oldSubscribedPack = $oldFeature->getSubscribedPack();
                        $newSubscribedPack = $newFeatures->get($oldFeature->getName())->getSubscribedPack();

                        // ... and then we compare them. If they are not equal...
                        if ($oldSubscribedPack->getNumOfUnits() !== $newSubscribedPack->getNumOfUnits()) {
                            // ... the pack was removed (changed)
                            $this->differences[self::REMOVED][] = [$oldFeature->getName() => $oldSubscribedPack->getNumOfUnits()];
                        }
                    }

                    break;
                case SubscribedRechargeableFeature::class:
                    break;
            }
        }

        /**
         * Calculate the added features.
         *
         * A feature is added if:
         * 1. It was not in the old collection but exists in the new collection;
         * 2. It was in the old collection and was not enabled and is in the new collection too but is enabled
         */
        foreach ($newFeatures as $newFeature) {
            /*
             * Here we first build the value to add as we need to distinguish immediately between the two kind of
             * features, boolean and countable, because if a CountableFeature is added, we need to know the subscribed
             * plan.
             */
            $featureDetails = '';
            switch (\get_class($newFeature)) {
                // If is a BooleanFeature...
                case SubscribedBooleanFeature::class:
                    // ... we simply need its name
                    $featureDetails = $newFeature->getName();

                    break;
                    // If is a CountableFeature...
                case SubscribedCountableFeature::class:
                    /** @var SubscribedCountableFeature $featureDetails */
                    $featureDetails = [$newFeature->getName() => $newFeature->getSubscribedPack()->getNumOfUnits()];

                    break;
                    // If is a CountableFeature...
                case SubscribedRechargeableFeature::class:
                    /** @var SubscribedRechargeableFeature $featureDetails */
                    $featureDetails = [$newFeature->getName() => $newFeature->getRechargingPack()->getNumOfUnits()];

                    break;
            }

            // ... If the feature was not in the old collection but exists in the new collection...
            if (false === $oldFeatures->containsKey($newFeature->getName())) {
                // ... It was added for sure
                $this->differences[self::ADDED][] = $featureDetails;

                continue;
            }

            // If the new feature already was in the old collection...
            if (true === $oldFeatures->containsKey($newFeature->getName())) {
                // We need to know which kind of feature we are checking to know how to do the check
                switch (\get_class($newFeature)) {
                    // If is a BooleanFeature...
                    case SubscribedBooleanFeature::class:
                        // If now, in the new subscription, is enabled...
                        if (true === $newFeature->isEnabled()
                            // ... But were not enabled in the old subscription
                            && false === $oldFeatures->get($newFeature->getName())->isEnabled()
                        ) {
                            // ... then, it was added
                            $this->differences[self::ADDED][] = $featureDetails;
                        }

                        break;
                        // If is a CountableFeature...
                    case SubscribedCountableFeature::class:
                        /** @var SubscribedCountableFeaturePack $newSubscribedPack */
                        $newSubscribedPack = $newFeature->getSubscribedPack();

                        /** @var SubscribedCountableFeaturePack $oldSubscribedPack */
                        $oldSubscribedPack = $oldFeatures->get($newFeature->getName())->getSubscribedPack();

                        // We first get the subscribed packages and then we compare them. If they are not equal...
                        if ($oldSubscribedPack->getNumOfUnits() !== $newSubscribedPack->getNumOfUnits()) {
                            // ... the pack was removed (changed)
                            $this->differences[self::ADDED][] = $featureDetails;
                        }

                        break;
                        // If it is a RechargeableFeature...
                    case SubscribedRechargeableFeature::class:
                        // ... if a rechargin pack exists...
                        if ($newFeature->hasRechargingPack()) {
                            // ... We are simply recharging the feature
                            $this->differences[self::ADDED][] = $featureDetails;
                        }

                        break;
                }
            }
        }

        return $this->getDifferences();
    }

    /**
     * Updates the amount of the next payment for the provided subscription object.
     */
    private function updateNextPaymentAmount(): void
    {
        $this->getSubscription()->setNextRenewAmount($this->calculateSubscriptionAmount());
    }

    /**
     * Updates the renew period based on Countable features present (to the smallest interval) and sets the next renew
     * date.
     */
    private function refreshCountableFeatures(): void
    {
        $refreshInterval = SubscriptionInterface::MONTHLY;
        /** @var SubscribedCountableFeature $feature */
        foreach ($this->getSubscription()->getFeatures()->getValues() as $feature) {
            if ($feature instanceof SubscribedCountableFeature) {
                /** @var ConfiguredCountableFeature $configuredFeature */
                $configuredFeature = $feature->getConfiguredFeature();

                // If the configured renew period is smaller than the current renew period...
                if (self::INTERVALS[$configuredFeature->getRefreshPeriod()] < self::INTERVALS[$refreshInterval]) {
                    // Set the configured renew period as the new current renew period
                    $refreshInterval = $configuredFeature->getRefreshPeriod();
                }

                // Refresh the feature
                $feature->refresh();
            }
        }

        $nextRefreshOn = $this->getSubscription()->getNextRefreshOn() ?? clone $this->getSubscription()->getSubscribedOn();
        $this->getSubscription()
            ->setSmallestRefreshInterval($refreshInterval)
            ->setNextRefreshOn($nextRefreshOn);
        switch ($this->getSubscription()->getSmallestRefreshInterval()) {
            // We need to clone the \DateTime object to change its reference
            // @see http://docs.doctrine-project.org/projects/doctrine-orm/en/latest/cookbook/working-with-datetime.html
            case SubscriptionInterface::DAILY:
                $this->getSubscription()->setNextRefreshOn(
                    clone $this->getSubscription()->getNextRefreshOn()->modify('+1 day')
                );

                break;
            case SubscriptionInterface::WEEKLY:
                $this->getSubscription()->setNextRefreshOn(
                    clone $this->getSubscription()->getNextRefreshOn()->modify('+1 week')
                );

                break;
            case SubscriptionInterface::BIWEEKLY:
                $this->getSubscription()->setNextRefreshOn(
                    clone $this->getSubscription()->getNextRefreshOn()->modify('+2 week')
                );

                break;
            case SubscriptionInterface::MONTHLY:
                $this->getSubscription()->setNextRefreshOn(
                    clone $this->getSubscription()->getNextRefreshOn()->modify('+1 month')
                );

                break;
            case SubscriptionInterface::YEARLY:
                $this->getSubscription()->setNextRefreshOn(
                    clone $this->getSubscription()->getNextRefreshOn()->modify('+1 year')
                );

                break;
        }
    }

    /**
     * Updates the date until the features in the Subscription are active.
     */
    private function updateUntilDates(): void
    {
        $validUntil = $this->getSubscription()->getNextRenewOn();

        /** @var array $feature */
        foreach ($this->getDifferences(self::ADDED) as $feature) {
            // If this is an array, this is a Package...
            if (\is_array($feature)) {
                // So we need the key of the array that is the feature's name
                $feature = \key($feature);
            }

            if (false === $this->getSubscription()->has($feature)) {
                $this->getSubscription()->addFeature(
                    $feature, $this->getConfiguredFeatures()->get($feature)
                );
            }

            /** @var FeatureInterface $updatingFeature */
            $updatingFeature = $this->getSubscription()->getFeatures()->get($feature);

            if ($updatingFeature instanceof IsRecurringFeatureInterface) {
                $updatingFeature->setActiveUntil($validUntil);
            }
        }
    }
}
