<?php

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
 * Implemented by features or packages that can be consumed (Countable and Rechargeable).
 */
interface CanBeConsumedInterface
{
    public const CONSUMED_QUANTITY = 'consumed_quantity';
    public const REMAINED_QUANTITY = 'remained_quantity';

    /**
     * Method to consume the given quantity of this feature.
     */
    public function consume(int $quantity): CanBeConsumedInterface;

    /**
     * Method to consume one unit of this feature.
     */
    public function consumeOne(): CanBeConsumedInterface;

    public function getConsumedQuantity(): int;

    public function getRemainedQuantity(): int;
}
