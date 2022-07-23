<?php

declare(strict_types=1);

/*
 * This file is part of the Serendipity HQ Features Bundle.
 *
 * Copyright (c) Adamo Aerendir Crespi <aerendir@serendipityhq.com>.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SerendipityHQ\Bundle\FeaturesBundle\Model\Feature\Property;

interface IsRecurringFeatureInterface
{
    public const FIELD_ACTIVE_UNTIL          = 'active_until';
    public const FIELD_ACTIVE_UNTIL_DATE     = 'date';
    public const FIELD_ACTIVE_UNTIL_TIMEZONE = 'timezone';

    /**
     * The date until which the feature is active.
     *
     * @return \DateTime|\DateTimeImmutable|null
     */
    public function getActiveUntil(): ?\DateTimeInterface;

    public function isStillActive(): bool;

    /**
     * Sets the date until which the feature is active.
     *
     * @param \DateTime|\DateTimeImmutable $activeUntil
     */
    public function setActiveUntil(\DateTimeInterface $activeUntil): IsRecurringFeatureInterface;
}
