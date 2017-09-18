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

/**
 * Concrete implementetion of the CanBeConsumedInterface.
 */
trait CanBeConsumedProperty
{
    /** @var int $consumedQuantity How many units are consumed at this time */
    private $consumedQuantity = 0;

    /** @var int $remaining The num of units remained from the last subscription cycle */
    private $remainedQuantity = 0;

    /**
     * {@inheritdoc}
     */
    public function consume(int $quantity): CanBeConsumedInterface
    {
        $this->consumedQuantity += $quantity;
        $this->remainedQuantity -= $quantity;

        /** @var CanBeConsumedInterface $this */
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function consumeOne(): CanBeConsumedInterface
    {
        return $this->consume(1);
    }

    /**
     * {@inheritdoc}
     */
    public function getConsumedQuantity(): int
    {
        return $this->consumedQuantity;
    }

    /**
     * {@inheritdoc}
     */
    public function getRemainedQuantity(): int
    {
        return $this->remainedQuantity;
    }

    /**
     * @return array
     */
    public function consumedToArray()
    {
        return [
            'consumed_quantity' => $this->getConsumedQuantity(),
            'remained_quantity' => $this->getRemainedQuantity(),
        ];
    }

    /**
     * Used internally only to set the value when the object is hydrated from the database.
     *
     * @param int $remainedQuantity
     *
     * @return CanBeConsumedInterface
     */
    protected function setRemainedQuantity(int $remainedQuantity): CanBeConsumedInterface
    {
        $this->remainedQuantity = $remainedQuantity;

        /** @var CanBeConsumedInterface $this */
        return $this;
    }
}
