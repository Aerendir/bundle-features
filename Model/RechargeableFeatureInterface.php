<?php

namespace SerendipityHQ\Bundle\FeaturesBundle\Model;

use SerendipityHQ\Bundle\FeaturesBundle\Property\SimplePricesInterface;

/**
 * {@inheritdoc}
 */
interface RechargeableFeatureInterface extends FeatureInterface, SimplePricesInterface
{
    /**
     * @return int
     */
    public function getFreeRecharge() : int;

    /**
     * @param int $freeRecharge
     * @return RechargeableFeatureInterface
     */
    public function setFreeRecharge(int $freeRecharge) : RechargeableFeatureInterface;

    /**
     * @param array $packs
     * @return RechargeableFeatureInterface
     */
    public function setPacks(array $packs) : RechargeableFeatureInterface;
}
