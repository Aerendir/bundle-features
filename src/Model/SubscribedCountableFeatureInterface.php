<?php

namespace SerendipityHQ\Bundle\FeaturesBundle\Model;

use SerendipityHQ\Bundle\FeaturesBundle\Property\CanBeConsumedInterface;
use SerendipityHQ\Bundle\FeaturesBundle\Property\IsRecurringFeatureInterface;

/**
 * {@inheritdoc}
 */
interface SubscribedCountableFeatureInterface extends SubscribedFeatureInterface, IsRecurringFeatureInterface, CanBeConsumedInterface
{
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
     * The date on which the feature were renew last time.
     *
     * This can return null so it is compatible with older versions of the Bundle.
     *
     * @return \DateTime|null
     */
    public function getLastRefreshOn() :? \DateTime;

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
     * Checks if the refresh period is elapsed for this feature.
     *
     * @return bool
     */
    public function isRefreshPeriodElapsed() : bool;

    /**
     * Renews the subscription resetting the available quantities.
     */
    public function refresh() : SubscribedCountableFeatureInterface;

    /**
     * Sets the date on which the renew happened.
     *
     * @param \DateTime $lastRenewOn
     *
     * @return SubscribedCountableFeatureInterface
     */
    public function setLastRefreshOn(\DateTime $lastRenewOn) : SubscribedCountableFeatureInterface;

    /**
     * @param SubscribedCountableFeaturePack $pack
     */
    public function setSubscribedPack(SubscribedCountableFeaturePack $pack);
}
