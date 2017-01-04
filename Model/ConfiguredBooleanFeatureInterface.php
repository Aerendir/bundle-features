<?php

namespace SerendipityHQ\Bundle\FeaturesBundle\Model;

use SerendipityHQ\Bundle\FeaturesBundle\Property\HasRecurringPricesInterface;

/**
 * {@inheritdoc}
 */
interface ConfiguredBooleanFeatureInterface extends HasRecurringPricesInterface, ConfiguredFeatureInterface
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
