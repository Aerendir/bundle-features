<?php

namespace SerendipityHQ\Bundle\FeaturesBundle\Model;

/**
 * {@inheritdoc}
 */
interface SubscribedRechargeableFeatureInterface extends SubscribedFeatureInterface, SubscribedRecurringFeatureInterface
{
    /**
     * @return int
     */
    public function getRechargeAmount() : int;

    /**
     * @param int $freeRecharge
     * @return SubscribedRechargeableFeatureInterface
     */
    public function setRechargeAmount(int $freeRecharge) : SubscribedRechargeableFeatureInterface;
}
