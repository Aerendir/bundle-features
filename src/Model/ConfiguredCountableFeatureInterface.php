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
    public function getRefreshPeriod() : string;

    /**
     * @param SubscriptionInterface $subscription
     * @return ConfiguredCountableFeatureInterface
     */
    public function setSubscription(SubscriptionInterface $subscription): ConfiguredCountableFeatureInterface;

    /**
     * @param float $rate
     * @param string $name
     * @return ConfiguredCountableFeatureInterface
     */
    public function setTax(float $rate, string $name): ConfiguredCountableFeatureInterface;
}
