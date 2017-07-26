<?php

namespace SerendipityHQ\Bundle\FeaturesBundle\Model;

use SerendipityHQ\Bundle\FeaturesBundle\Property\CanHaveFreePackInterface;
use SerendipityHQ\Bundle\FeaturesBundle\Property\HasConfiguredPacksInterface;

/**
 * {@inheritdoc}
 */
interface ConfiguredCountableFeatureInterface extends HasConfiguredPacksInterface, CanHaveFreePackInterface, ConfiguredFeatureInterface
{
    /**
     * @return string
     */
    public function getRenewPeriod() : string;

    /**
     * @param SubscriptionInterface $subscription
     * @return ConfiguredCountableFeatureInterface
     */
    public function setSubscription(SubscriptionInterface $subscription): ConfiguredCountableFeatureInterface;

    /**
     * @param float $rate
     * @return ConfiguredCountableFeatureInterface
     */
    public function setTaxRate(float $rate): ConfiguredCountableFeatureInterface;
}
