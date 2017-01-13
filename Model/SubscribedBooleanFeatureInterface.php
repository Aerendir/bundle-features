<?php

namespace SerendipityHQ\Bundle\FeaturesBundle\Model;

use SerendipityHQ\Bundle\FeaturesBundle\Property\IsRecurringFeatureInterface;

/**
 * {@inheritdoc}
 */
interface SubscribedBooleanFeatureInterface extends IsRecurringFeatureInterface, SubscribedFeatureInterface
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
