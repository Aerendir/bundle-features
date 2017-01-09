<?php

namespace SerendipityHQ\Bundle\FeaturesBundle\Model;

/**
 * {@inheritdoc}
 */
interface SubscribedRechargeableFeatureInterface extends SubscribedFeatureInterface
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
