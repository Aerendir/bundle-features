<?php

namespace SerendipityHQ\Bundle\FeaturesBundle\Model;

use SerendipityHQ\Bundle\FeaturesBundle\Property\HasPacksInterface;
use SerendipityHQ\Bundle\FeaturesBundle\Property\HasUnatantumPricesInterface;

/**
 * {@inheritdoc}
 */
interface ConfiguredRechargeableFeatureInterface extends ConfiguredFeatureInterface, HasUnatantumPricesInterface, HasPacksInterface
{
    /**
     * @return int
     */
    public function getFreeRecharge() : int;

    /**
     * @param int $freeRecharge
     * @return ConfiguredRechargeableFeatureInterface
     */
    public function setFreeRecharge(int $freeRecharge) : ConfiguredRechargeableFeatureInterface;
}
