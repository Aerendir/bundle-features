<?php

/*
 * This file is part of the Serendipity HQ Features Bundle.
 *
 * Copyright (c) Adamo Aerendir Crespi <aerendir@serendipityhq.com>.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SerendipityHQ\Bundle\FeaturesBundle\Property;

/**
 * Implemented by features or packages that can be consumed (Countable and Rechargeable).
 */
interface CanBeConsumedInterface
{
    /**
     * Method to consume the given quantity of this feature.
     *
     * @param int $quantity
     *
     * @return CanBeConsumedInterface
     */
    public function consume(int $quantity): CanBeConsumedInterface;

    /**
     * Method to consume one unit of this feature.
     *
     * @return CanBeConsumedInterface
     */
    public function consumeOne(): CanBeConsumedInterface;

    /**
     * @return int
     */
    public function getConsumedQuantity(): int;

    /**
     * @return int
     */
    public function getRemainedQuantity(): int;
}
