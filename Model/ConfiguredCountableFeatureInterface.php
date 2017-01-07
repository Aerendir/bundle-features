<?php

namespace SerendipityHQ\Bundle\FeaturesBundle\Model;

use SerendipityHQ\Bundle\FeaturesBundle\Property\HasPacksInterface;
use SerendipityHQ\Bundle\FeaturesBundle\Property\HasRecurringPricesInterface;

/**
 * {@inheritdoc}
 */
interface ConfiguredCountableFeatureInterface extends HasRecurringPricesInterface, HasPacksInterface, ConfiguredFeatureInterface
{
    /**
     * @return int
     */
    public function getFreeAmount() : int;
}
