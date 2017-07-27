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
     * @return SubscribedRechargeableFeaturePack
     */
    public function getRechargingPack() : SubscribedRechargeableFeaturePack;

    /**
     * @return int
     */
    public function getRemainedQuantity() : int;

    /**
     * @return bool
     */
    public function hasRechargingPack() : bool;

    /**
     * @return SubscribedRechargeableFeatureInterface
     */
    public function recharge() : SubscribedRechargeableFeatureInterface;

    /**
     * @param SubscribedRechargeableFeaturePack $rechargingPack
     * @return SubscribedRechargeableFeatureInterface
     */
    public function setRecharginPack(SubscribedRechargeableFeaturePack $rechargingPack) : SubscribedRechargeableFeatureInterface;
}
