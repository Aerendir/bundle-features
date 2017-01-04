<?php

namespace SerendipityHQ\Bundle\FeaturesBundle\Model;

use SerendipityHQ\Bundle\FeaturesBundle\Property\HasUnatantumPricesInterface;

/**
 * {@inheritdoc}
 */
interface SubscribedRechargeableFeatureInterface extends SubscribedFeatureInterface, HasUnatantumPricesInterface
{
    /**
     * @return int
     */
    public function getFreeRecharge() : int;

    /**
     * @param int $freeRecharge
     * @return SubscribedRechargeableFeatureInterface
     */
    public function setFreeRecharge(int $freeRecharge) : SubscribedRechargeableFeatureInterface;
}
