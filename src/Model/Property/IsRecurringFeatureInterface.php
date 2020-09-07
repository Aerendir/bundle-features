<?php

/*
 * This file is part of the Serendipity HQ Features Bundle.
 *
 * Copyright (c) Adamo Aerendir Crespi <aerendir@serendipityhq.com>.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SerendipityHQ\Bundle\FeaturesBundle\Model\Property;

interface IsRecurringFeatureInterface
{
    /**
     * The date until which the feature is active.
     */
    public function getActiveUntil(): ?\DateTime;

    public function isStillActive(): bool;

    /**
     * Sets the date until which the feature is active.
     */
    public function setActiveUntil(\DateTime $activeUntil): IsRecurringFeatureInterface;
}
