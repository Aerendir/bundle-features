<?php

/*
 * This file is part of the SHQFeaturesBundle.
 *
 * Copyright Adamo Aerendir Crespi 2016-2017.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    Adamo Aerendir Crespi <hello@aerendir.me>
 * @copyright Copyright (C) 2016 - 2017 Aerendir. All rights reserved.
 * @license   MIT License.
 */

namespace SerendipityHQ\Bundle\FeaturesBundle\Property;

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
    public function isStillActive(): bool;

    /**
     * Sets the date until which the feature is active.
     *
     * @param \DateTime $activeUntil
     *
     * @return IsRecurringFeatureInterface
     */
    public function setActiveUntil(\DateTime $activeUntil): IsRecurringFeatureInterface;
}
