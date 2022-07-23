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
 * Concrete implementetion of the CanBeConsumedInterface.
 */
trait CanBeConsumedProperty
{
    /** @var int $consumedQuantity How many units are consumed at this time */
    private $consumedQuantity = 0;

    /** @var int $remaining The num of units remained from the last subscription cycle */
    private $remainedQuantity = 0;

    public function consume(int $quantity): CanBeConsumedInterface
    {
        $this->consumedQuantity += $quantity;
        $this->remainedQuantity -= $quantity;

        /** @var CanBeConsumedInterface $this */
        return $this;
    }

    public function consumeOne(): CanBeConsumedInterface
    {
        return $this->consume(1);
    }

    public function getConsumedQuantity(): int
    {
        return $this->consumedQuantity;
    }

    public function getRemainedQuantity(): int
    {
        return $this->remainedQuantity;
    }

    public function consumedToArray(): array
    {
        return [
            CanBeConsumedInterface::CONSUMED_QUANTITY => $this->getConsumedQuantity(),
            CanBeConsumedInterface::REMAINED_QUANTITY => $this->getRemainedQuantity(),
        ];
    }

    /**
     * Used internally only to set the value when the object is hydrated from the database.
     */
    protected function setRemainedQuantity(int $remainedQuantity): CanBeConsumedInterface
    {
        $this->remainedQuantity = $remainedQuantity;

        /** @var CanBeConsumedInterface $this */
        return $this;
    }
}
