<?php

namespace SerendipityHQ\Bundle\FeaturesBundle\Model;

use SerendipityHQ\Bundle\FeaturesBundle\Property\CanBeConsumedInterface;

/**
 * {@inheritdoc}
 */
interface SubscribedRechargeableFeatureInterface extends SubscribedFeatureInterface, CanBeConsumedInterface
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
