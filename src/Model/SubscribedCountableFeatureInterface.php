<?php

namespace SerendipityHQ\Bundle\FeaturesBundle\Model;

use SerendipityHQ\Bundle\FeaturesBundle\Property\IsRecurringFeatureInterface;

/**
 * {@inheritdoc}
 */
interface SubscribedCountableFeatureInterface extends SubscribedFeatureInterface, IsRecurringFeatureInterface
{
    /**
     * Method to consume the given quantity of this feature.
     *
     * @param int $quantity
     * @return SubscribedCountableFeatureInterface
     */
    public function consume(int $quantity) : SubscribedCountableFeatureInterface;

    /**
     * Method to consume one unit of this feature.
     *
     * @return SubscribedCountableFeatureInterface
     */
    public function consumeOne() : SubscribedCountableFeatureInterface;

    /**
     * Adds the previous remained amount to the refreshed subscription quantity.
     *
     * So, if the current quantity is 4 and a recharge(5) is made, the new $remainedQuantity is 5.
     * But if cumulate() is called, the new $remainedQuantity is 9:
     *
     *     ($previousRemainedQuantity = 4) + ($rechargeQuantity = 5).
     *
     * @return SubscribedCountableFeatureInterface
     */
    public function cumulate() : SubscribedCountableFeatureInterface;

    /**
     * @return int
     */
    public function getConsumedQuantity() : int;

    /**
     * The date on which the feature were renew last time.
     *
     * @return \DateTime|null
     */
    public function getLastRenewOn() :? \DateTime;

    /**
     * @return int
     */
    public function getRemainedQuantity() : int;

    /**
     * It is an integer when the feature is loaded from the database.
     *
     * Then, once called FeaturesManager::setSubscription(), this is transformed into the correspondent
     * ConfiguredFeaturePackInterface object.
     *
     * @return int|ConfiguredFeaturePackInterface
     */
    public function getSubscribedPack();

    /**
     * Checks if the renew period is elapsed for this feature.
     *
     * @return bool
     */
    public function isRenewPeriodElapsed() : bool;

    /**
     * Sets the date on which the renew happened.
     *
     * @param \DateTime $lastRenewOn
     *
     * @return SubscribedCountableFeatureInterface
     */
    public function setLastRenewOn(\DateTime $lastRenewOn) : SubscribedCountableFeatureInterface;

    /**
     * @param SubscribedCountableFeaturePack $pack
     */
    public function setSubscribedPack(SubscribedCountableFeaturePack $pack);
}
