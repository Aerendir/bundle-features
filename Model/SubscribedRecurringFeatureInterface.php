<?php

namespace SerendipityHQ\Bundle\FeaturesBundle\Model;

/**
 * {@inheritdoc}
 */
interface SubscribedRecurringFeatureInterface extends FeatureInterface
{
    /**
     * The date until which the feature is active.
     *
     * @return \DateTime|null
     */
    public function getActiveUntil();

    /**
     * @return bool
     */
    public function isStillActive() : bool;

    /**
     * Sets the date until which the feature is active.
     *
     * @param \DateTime $activeUntil
     *
     * @return SubscribedRecurringFeatureInterface
     */
    public function setActiveUntil(\DateTime $activeUntil) : SubscribedRecurringFeatureInterface;
}
