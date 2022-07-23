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

/**
 * Manages properties of a Recurring feature.
 */
trait IsRecurringFeatureProperty
{
    /** @var \DateTime $activeUntil */
    private $activeUntil;

    public function __construct(array $details = [])
    {
        if (isset($details[IsRecurringFeatureInterface::FIELD_ACTIVE_UNTIL])) {
            $this->activeUntil = $details[IsRecurringFeatureInterface::FIELD_ACTIVE_UNTIL] instanceof \DateTime ? $details[IsRecurringFeatureInterface::FIELD_ACTIVE_UNTIL] : new \DateTime($details[IsRecurringFeatureInterface::FIELD_ACTIVE_UNTIL][IsRecurringFeatureInterface::FIELD_ACTIVE_UNTIL_DATE], new \DateTimeZone($details[IsRecurringFeatureInterface::FIELD_ACTIVE_UNTIL][IsRecurringFeatureInterface::FIELD_ACTIVE_UNTIL_TIMEZONE]));
        }
    }

    /**
     * @return \DateTime|\DateTimeImmutable|null
     */
    public function getActiveUntil(): ?\DateTimeInterface
    {
        return $this->activeUntil;
    }

    public function isStillActive(): bool
    {
        if (null === $this->getActiveUntil()) {
            return false;
        }

        return $this->getActiveUntil() >= new \DateTime();
    }

    /**
     * @param \DateTime|\DateTimeImmutable $activeUntil
     */
    public function setActiveUntil(\DateTimeInterface $activeUntil): IsRecurringFeatureInterface
    {
        $this->activeUntil = $activeUntil;

        /** @var IsRecurringFeatureInterface $this */
        return $this;
    }
}
