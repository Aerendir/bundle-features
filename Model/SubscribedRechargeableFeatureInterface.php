<?php

namespace SerendipityHQ\Bundle\FeaturesBundle\Model;

use SerendipityHQ\Bundle\FeaturesBundle\Property\HasQuantitiesInterface;

/**
 * {@inheritdoc}
 */
interface SubscribedRechargeableFeatureInterface extends SubscribedFeatureInterface, HasQuantitiesInterface
{
    /**
     * @return \DateTime
     */
    public function getLastRechargeOn() : \DateTime;

    /**
     * @return int
     */
    public function getLastRechargeQuantity() : int;

    /**
     * @param int $freeRecharge
     * @return SubscribedRechargeableFeatureInterface
     */
    public function recharge(int $freeRecharge) : SubscribedRechargeableFeatureInterface;
}
