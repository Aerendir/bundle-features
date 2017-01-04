<?php

namespace SerendipityHQ\Bundle\FeaturesBundle\Model;

/**
 * {@inheritdoc}
 */
interface ConfiguredCountableFeatureInterfaceConfigured extends ConfiguredRecurringFeatureInterface
{
    /**
     * @return int
     */
    public function getFreeAmount() : int;
}
