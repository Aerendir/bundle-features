<?php

namespace SerendipityHQ\Bundle\FeaturesBundle\Property;

/**
 * {@inheritdoc}
 */
interface IsRecurringFeatureInterface
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
     * @return IsRecurringFeatureInterface
     */
    public function setActiveUntil(\DateTime $activeUntil) : IsRecurringFeatureInterface;
}
