<?php

namespace SerendipityHQ\Bundle\FeaturesBundle\Model;

/**
 * A Feature loaded from a subscription.
 */
interface SubscribedFeatureInterface extends FeatureInterface
{
    /**
     * @return ConfiguredFeatureInterface
     */
    public function getConfiguredFeature() : ConfiguredFeatureInterface;

    /**
     * @param ConfiguredFeatureInterface $configuredFeature
     */
    public function setConfiguredFeature(ConfiguredFeatureInterface $configuredFeature);

    /**
     * Converts a Feature object into an array.
     *
     * @return array
     */
    public function toArray();
}
