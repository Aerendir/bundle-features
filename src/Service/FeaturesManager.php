<?php

/*
 * This file is part of the Serendipity HQ Features Bundle.
 *
 * Copyright (c) Adamo Aerendir Crespi <aerendir@serendipityhq.com>.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SerendipityHQ\Bundle\FeaturesBundle\Service;

use SerendipityHQ\Bundle\FeaturesBundle\Form\DataTransformer\FeaturesCollectionTransformer;
use SerendipityHQ\Bundle\FeaturesBundle\Form\Type\FeaturesType;
use SerendipityHQ\Bundle\FeaturesBundle\Model\ConfiguredBooleanFeatureInterface;
use SerendipityHQ\Bundle\FeaturesBundle\Model\ConfiguredCountableFeatureInterface;
use SerendipityHQ\Bundle\FeaturesBundle\Model\ConfiguredFeaturesCollection;
use SerendipityHQ\Bundle\FeaturesBundle\Model\ConfiguredRechargeableFeatureInterface;
use SerendipityHQ\Bundle\FeaturesBundle\Model\FeatureInterface;
use SerendipityHQ\Bundle\FeaturesBundle\Model\SubscribedBooleanFeature;
use SerendipityHQ\Bundle\FeaturesBundle\Model\SubscribedBooleanFeatureInterface;
use SerendipityHQ\Bundle\FeaturesBundle\Model\SubscribedCountableFeature;
use SerendipityHQ\Bundle\FeaturesBundle\Model\SubscribedCountableFeatureInterface;
use SerendipityHQ\Bundle\FeaturesBundle\Model\SubscribedCountableFeaturePack;
use SerendipityHQ\Bundle\FeaturesBundle\Model\SubscribedFeatureInterface;
use SerendipityHQ\Bundle\FeaturesBundle\Model\SubscribedFeaturesCollection;
use SerendipityHQ\Bundle\FeaturesBundle\Model\SubscribedRechargeableFeature;
use SerendipityHQ\Bundle\FeaturesBundle\Model\SubscribedRechargeableFeatureInterface;
use SerendipityHQ\Bundle\FeaturesBundle\Model\Subscription;
use SerendipityHQ\Bundle\FeaturesBundle\Model\SubscriptionInterface;
use SerendipityHQ\Bundle\FeaturesBundle\Property\IsRecurringFeatureInterface;
use SerendipityHQ\Component\ValueObjects\Money\Money;
use SerendipityHQ\Component\ValueObjects\Money\MoneyInterface;
use Symfony\Component\Form\Extension\Core\Type\FormType;
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

    /** @var SubscriptionInterface $subscription This is use to calculate added and removed boolean features and the changed packs of CountableFeatures */
    private $oldSubscription;

    /** @var array $differences The added and removed features */
    private $differences = [
        'added'   => [],
        'removed' => [],
    ];

    /**
     * @param array $configuredFeatures
     */
    public function __construct(array $configuredFeatures)
    {
        $this->configuredFeatures = new ConfiguredFeaturesCollection($configuredFeatures);
    }

    /**
     * Returns all the configured features.
     *
     * @return ConfiguredFeaturesCollection
     */
    public function getConfiguredFeatures(): ConfiguredFeaturesCollection
    {
        return $this->configuredFeatures;
    }

    /**
     * @return SubscriptionInterface
     */
    public function getSubscription(): SubscriptionInterface
    {
        return $this->subscription;
    }

    /**
     * @param SubscriptionInterface $subscription
     *
     * @return FeaturesManager
     */
    public function setSubscription(SubscriptionInterface $subscription): self
    {
        $this->subscription = $subscription;
        $this->getInvoicesManager()->setSubscription($this->getSubscription());
        $this->getConfiguredFeatures()->setSubscription($this->getSubscription());

        /**
         * Set the Configured feature in each subscribed feature.
         *
         * @var SubscribedFeatureInterface
         */
        foreach ($subscription->getFeatures()->getValues() as $subscribedFeature) {
            $configuredFeature = $this->getConfiguredFeatures()->get($subscribedFeature->getName());

            // If the feature doesn't exist anymore in configuraiton, skip it: it will be deleted on next subscription update
            if (null !== $configuredFeature) {
                $subscribedFeature->setConfiguredFeature($configuredFeature);
            }
        }

        return $this;
    }

    /**
     * @param float  $rate
     * @param string $name
     *
     * @return self
     */
    public function setTax(float $rate, string $name): self
    {
        $this->getConfiguredFeatures()->setTax($rate, $name);
        $this->getInvoicesManager()->getConfiguredFeatures()->setTax($rate, $name);

        return $this;
    }

    /**
     * @param string $subscriptionInterval
     *
     * @throws \InvalidArgumentException If the $subscriptionInterval does not exist
     *
     * @return SubscribedFeaturesCollection
     */
    public function buildDefaultSubscriptionFeatures(string $subscriptionInterval): SubscribedFeaturesCollection
    {
        $activeUntil = Subscription::calculateActiveUntil($subscriptionInterval);
        $features    = [];

        /**
         * @var string
         * @var ConfiguredBooleanFeatureInterface|ConfiguredCountableFeatureInterface|ConfiguredRechargeableFeatureInterface|FeatureInterface $details
         */
        foreach ($this->getConfiguredFeatures() as $name => $details) {
            switch ($details->getType()) {
                case 'boolean':
                    /** @var ConfiguredBooleanFeatureInterface $details */
                    $features[$name] = [
                        'active_until' => false === $this->getConfiguredFeatures()->get($name)->isEnabled() ? null : $activeUntil,
                        'type'         => $details->getType(),
                        'enabled'      => $details->isEnabled(),
                    ];
                    break;
                case 'countable':
                    /** @var ConfiguredCountableFeatureInterface $details */
                    $features[$name] = [
                        'type'              => $details->getType(),
                        'subscribed_pack'   => ['num_of_units' => $this->getConfiguredFeatures()->get($name)->getFreePack()->getNumOfUnits()],
                        'remained_quantity' => $this->getConfiguredFeatures()->get($name)->getFreePack()->getNumOfUnits(),
                    ];
                    break;
                case 'rechargeable':
                    /** @var ConfiguredRechargeableFeatureInterface $details */
                    $features[$name] = [
                        'type'                   => $details->getType(),
                        'last_recharge_on'       => new \DateTime(),
                        'last_recharge_quantity' => $this->getConfiguredFeatures()->get($name)->getFreeRecharge(),
                        'remained_quantity'      => $this->getConfiguredFeatures()->get($name)->getFreeRecharge(),
                    ];
                    break;
            }
        }

        return new SubscribedFeaturesCollection($features);
    }

    /**
     * @param SubscribedFeaturesCollection $newFeatures This comes from the form, not from the Subscription! The Subscription is
     *                                                  not yet synced with these new Features!
     *
     * @return Money
     */
    public function calculateTotalChargesForNewFeatures(SubscribedFeaturesCollection $newFeatures)
    {
        $totalCharges = new Money(['baseAmount' => 0, 'currency' => $this->getSubscription()->getCurrency()]);

        // Calculate the added and removed Boolena features and the changed packages in Countable features
        $this->findDifferences($newFeatures);

        /*
         * May happen that a premium feature is activate and paid, then is deactivated but it is still in the subscription interval.
         * If it is activated again during the subscription interval, it were already paid, so it hasn't to be paid again.
         */
        foreach ($this->getDifferences('added') as $feature) {
            $featureName = is_array($feature) ? key($feature) : $feature;
            /** @var SubscribedBooleanFeatureInterface|SubscribedCountableFeatureInterface|SubscribedRechargeableFeatureInterface $checkingFeature */
            $checkingFeature = $this->getSubscription()->getFeatures()->get($featureName);

            if (null !== $checkingFeature) {
                /** @var ConfiguredBooleanFeatureInterface|ConfiguredCountableFeatureInterface|ConfiguredRechargeableFeatureInterface $configuredFeature */
                $configuredFeature = $this->getConfiguredFeatures()->get($featureName);
                $price             = null;

                switch (get_class($checkingFeature)) {
                    // These two have recurring features, so they can or cannot be still active
                    case SubscribedBooleanFeature::class:
                        if (true === $checkingFeature->isStillActive()) {
                            // If it is still active, we have to charge nothing, so continue processing next feature
                            continue;
                        }
                        $price = $configuredFeature->getInstantPrice($this->getSubscription()->getCurrency(), $this->getSubscription()->getRenewInterval(), 'gross');
                        break;
                    case SubscribedCountableFeature::class:
                        // @todo Support unitary_prices for CountableFeatures https://github.com/Aerendir/bundle-features/issues/1
                        if ($configuredFeature instanceof ConfiguredCountableFeatureInterface) {
                            /**
                             * For the moment force the code to get the pack's instant price.
                             *
                             * @var SubscribedCountableFeatureInterface
                             */
                            $price = $configuredFeature->getPack($checkingFeature->getSubscribedPack()->getNumOfUnits())->getInstantPrice($this->getSubscription()->getCurrency(), $this->getSubscription()->getRenewInterval(), 'gross');
                        }
                        break;
                    // A RechargeableFeature hasn't a subscription period, so it hasn't an isStillActive() method
                    case SubscribedRechargeableFeature::class:
                        /**
                         * For the moment force the code to get the pack's instant price.
                         *
                         * @var SubscribedRechargeableFeatureInterface
                         */
                        $price = $configuredFeature->getPack($checkingFeature->getRechargingPack()->getNumOfUnits())->getPrice($this->getSubscription()->getCurrency(), 'gross');
                        break;
                }

                if ($price instanceof MoneyInterface) {
                    $totalCharges = $totalCharges->add($price);
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
        // Generate this form only once
        static $form = null;

        if (null === $form) {
            // Set the subscription in the manager if not already done outside of the bundle by the implementing app
            // Here we assume this features manager is used ever with the same subscription
            if (null === $this->subscription) {
                $this->setSubscription($subscription);
            }

            if (false === $this->getConfiguredFeatures()->isTaxSet()) {
                throw new \RuntimeException('To generate a valid form you have to set a Tax. Call first setTax() and then you\'ll can call getFeaturesFormBuilder(). Ex.: FeaturesManager::setTax()->getFeaturesFormBuilder()');
            }

            // Clone the $subscription so we can use it to compare changes
            $this->oldSubscription = clone $subscription;

            $form = $this->getFormFactory()->createBuilder(FormType::class, [
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
     * @return ConfiguredFeaturesCollection
     *
     * @todo Method to implement
     */
    public function getPremiumFeaturesReview()
    {
        return $this->getConfiguredFeatures();
    }

    /**
     * Reverts the Subscription to the state before the editings.
     */
    public function rollbackSubscription()
    {
        $this->subscription
            ->setCurrency($this->oldSubscription->getCurrency())
            ->setFeatures($this->oldSubscription->getFeatures())
            ->setRenewInterval($this->oldSubscription->getRenewInterval())
            ->setNextRenewAmount($this->oldSubscription->getNextRenewAmount())
            ->setNextRenewOn($this->oldSubscription->getNextRenewOn());
    }

    /**
     * @param SubscriptionInterface        $subscription
     * @param SubscribedFeaturesCollection $features
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
     * It updates the next payment amount and the dates until the features are active.
     *
     * @param SubscribedFeaturesCollection|null $newFeatures
     */
    public function updateSubscription(SubscribedFeaturesCollection $newFeatures = null)
    {
        if (null !== $newFeatures) {
            /**
             * Before all, update the features, setting the new enabled status or adding the feature if not already present.
             *
             * @var FeatureInterface
             */
            foreach ($newFeatures as $newFeature) {
                $existentFeature = $this->getSubscription()->getFeatures()->get($newFeature->getName());

                // @todo Is this required? Didn't the form already updated the Subscription object? In fact I have an oldSubscription
                if ($existentFeature instanceof SubscribedBooleanFeatureInterface) {
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
    public function refreshSubscription()
    {
        $subscription = $this->getSubscription();

        /** @var FeatureInterface $feature */
        foreach ($subscription->getFeatures()->getValues() as $feature) {
            // If this is not a Countable Feature...
            if ( ! $feature instanceof SubscribedCountableFeatureInterface) {
                // Simply continue as it hasn't be renew
                continue;
            }

            /** @var ConfiguredCountableFeatureInterface $configuredRenewingFeature Get the configured feature * */
            $configuredRenewingFeature = $this->getConfiguredFeatures()->get($feature->getName());

            // If the feature doesn't exist anymore in the configuration (as it were removed by the developer)
            if (null === $configuredRenewingFeature) {
                // Remvoe it from the Subscription too
                $subscription->getFeatures()->removeElement($feature);

                // And continue with the next feature
                continue;
            }

            /** @var SubscribedCountableFeatureInterface $feature Refresh the feature if the refresh period is elapsed * */
            if ($feature->isRefreshPeriodElapsed()) {
                $feature->refresh();
            }
        }

        $this->refreshCountableFeatures();

        $subscription->forceFeaturesUpdate();
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
    public function getFormFactory(): FormFactory
    {
        return $this->formFactory;
    }

    /**
     * @return InvoicesManager
     */
    public function getInvoicesManager(): InvoicesManager
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

    /**
     * @return MoneyInterface
     */
    private function calculateSubscriptionAmount(): MoneyInterface
    {
        $total = new Money(['baseAmount' => 0, 'currency' => $this->getSubscription()->getCurrency()]);

        /** @var FeatureInterface $feature */
        foreach ($this->getSubscription()->getFeatures() as $feature) {
            // Check if the feature is still present in configuration
            if (null === $this->getConfiguredFeatures()->get($feature->getName())) {
                // It is not present anymore: remove it from the subscription
                $this->getSubscription()->getFeatures()->remove($feature->getName());

                // Simply Continue the cycle
                continue;
            }

            if ($feature instanceof SubscribedBooleanFeatureInterface && $feature->isEnabled()) {
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
     * @param SubscribedFeaturesCollection $newFeatures
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
         *
         * @var FeatureInterface
         */
        foreach ($oldFeatures as $oldFeature) {
            // If the Feature is in the old collection but doesn't exist in the new collection...
            if (false === $newFeatures->containsKey($oldFeature->getName())) {
                // ... It was removed and in this case we can simply set it as removed as we don't need much details
                $this->differences['removed'][] = $oldFeature->getName();
                continue;
            }

            switch (get_class($oldFeature)) {
                // If is a BooleanFeature...
                case SubscribedBooleanFeature::class:
                    /** @var SubscribedBooleanFeature $oldFeature */
                    // ... and was in the old collection and was enabled and is in the new collection but is not enabled...
                    if (true === $oldFeature->isEnabled()
                        && true === $newFeatures->containsKey($oldFeature->getName())
                        && false === $newFeatures->get($oldFeature->getName())->isEnabled()
                    ) {
                        // ... It was removed
                        $this->differences['removed'][] = $oldFeature->getName();
                    }
                    break;
                // If is a CountableFeature...
                case SubscribedCountableFeature::class:
                    /**
                     * ... and was in the old collection and in the new collection, too ...
                     *
                     * @var SubscribedCountableFeatureInterface
                     */
                    if (true === $newFeatures->containsKey($oldFeature->getName())) {
                        /**
                         * We first get the subscribed packages...
                         *
                         * @var SubscribedCountableFeaturePack
                         * @var SubscribedCountableFeaturePack $newSubscribedPack
                         */
                        $oldSubscribedPack = $oldFeature->getSubscribedPack();
                        $newSubscribedPack = $newFeatures->get($oldFeature->getName())->getSubscribedPack();

                        // ... and then we compare them. If they are not equal...
                        if ($oldSubscribedPack->getNumOfUnits() !== $newSubscribedPack->getNumOfUnits()) {
                            // ... the pack was removed (changed)
                            $this->differences['removed'][] = [$oldFeature->getName() => $oldSubscribedPack->getNumOfUnits()];
                        }
                    }
                    break;
                case SubscribedRechargeableFeature::class:
                    // Just as a placeholder for clarity. A RechargeableFeature cannot be removed.
                    continue;
                    break;
            }
        }

        /**
         * Calculate the added features.
         *
         * A feature is added if:
         * 1. It was not in the old collection but exists in the new collection;
         * 2. It was in the old collection and was not enabled and is in the new collection too but is enabled
         *
         * @var SubscribedBooleanFeatureInterface|SubscribedCountableFeatureInterface|SubscribedRechargeableFeatureInterface
         */
        foreach ($newFeatures as $newFeature) {
            /*
             * Here we first build the value to add as we need to distinguish immediately between the two kind of
             * features, boolean and countable, because if a CountableFeature is added, we need to know the subscribed
             * plan.
             */
            $featureDetails = '';
            switch (get_class($newFeature)) {
                // If is a BooleanFeature...
                case SubscribedBooleanFeature::class:
                    // ... we simply need its name
                    $featureDetails = $newFeature->getName();
                    break;
                // If is a CountableFeature...
                case SubscribedCountableFeature::class:
                    /** @var SubscribedCountableFeatureInterface $featureDetails */
                    $featureDetails = [$newFeature->getName() => $newFeature->getSubscribedPack()->getNumOfUnits()];
                    break;
                // If is a CountableFeature...
                case SubscribedRechargeableFeature::class:
                    /** @var SubscribedRechargeableFeatureInterface $featureDetails */
                    $featureDetails = [$newFeature->getName() => $newFeature->getRechargingPack()->getNumOfUnits()];
                    break;
            }

            // ... If the feature was not in the old collection but exists in the new collection...
            if (false === $oldFeatures->containsKey($newFeature->getName())) {
                // ... It was added for sure
                $this->differences['added'][] = $featureDetails;
                continue;
            }

            // If the new feature already was in the old collection...
            if (true === $oldFeatures->containsKey($newFeature->getName())) {
                // We need to know which kind of feature we are checking to know how to do the check
                switch (get_class($newFeature)) {
                    // If is a BooleanFeature...
                    case SubscribedBooleanFeature::class:
                        // If now, in the new subscription, is enabled...
                        if (true === $newFeature->isEnabled()
                            // ... But were not enabled in the old subscription
                            && false === $oldFeatures->get($newFeature->getName())->isEnabled()
                        ) {
                            // ... then, it was added
                            $this->differences['added'][] = $featureDetails;
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
                            $this->differences['added'][] = $featureDetails;
                        }
                        break;
                    // If it is a RechargeableFeature...
                    case SubscribedRechargeableFeature::class:
                        // ... if a rechargin pack exists...
                        if ($newFeature->hasRechargingPack()) {
                            // ... We are simply recharging the feature
                            $this->differences['added'][] = $featureDetails;
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
    private function updateNextPaymentAmount()
    {
        $this->getSubscription()->setNextRenewAmount($this->calculateSubscriptionAmount());
    }

    /**
     * Updates the renew period based on Countable features present (to the smallest interval) and sets the next renew
     * date.
     */
    private function refreshCountableFeatures()
    {
        $intervals = [
            SubscriptionInterface::DAILY    => 0,
            SubscriptionInterface::WEEKLY   => 1,
            SubscriptionInterface::BIWEEKLY => 2,
            SubscriptionInterface::MONTHLY  => 3,
            SubscriptionInterface::YEARLY   => 4,
        ];
        $refreshInterval = SubscriptionInterface::MONTHLY;

        /** @var SubscribedCountableFeatureInterface $feature */
        foreach ($this->getSubscription()->getFeatures()->getValues() as $feature) {
            if ($feature instanceof SubscribedCountableFeatureInterface) {
                /** @var ConfiguredCountableFeatureInterface $configuredFeature */
                $configuredFeature = $feature->getConfiguredFeature();

                // If the configured renew period is smaller than the current renew period...
                if ($intervals[$configuredFeature->getRefreshPeriod()] < $intervals[$refreshInterval]) {
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
    private function updateUntilDates()
    {
        $validUntil = $this->getSubscription()->getNextRenewOn();

        /** @var array $feature */
        foreach ($this->getDifferences('added') as $feature) {
            // If this is an array, this is a Package...
            if (is_array($feature)) {
                // So we need the key of the array that is the feature's name
                $feature = key($feature);
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
