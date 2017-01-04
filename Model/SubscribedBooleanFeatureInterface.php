<?php

namespace SerendipityHQ\Bundle\FeaturesBundle\Model;

/**
 * {@inheritdoc}
 */
interface SubscribedBooleanFeatureInterface extends SubscribedFeatureInterface, SubscribedRecurringFeatureInterface
{
    /**
     * @return FeatureInterface
     */
    public function disable() : FeatureInterface;

    /**
     * @return FeatureInterface
     */
    public function enable() : FeatureInterface;

    /**
     * @return bool
     */
    public function isEnabled() : bool;
}
