<?php

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
     * @return CanBeConsumedInterface
     */
    public function consume(int $quantity) : CanBeConsumedInterface;

    /**
     * Method to consume one unit of this feature.
     *
     * @return CanBeConsumedInterface
     */
    public function consumeOne() : CanBeConsumedInterface;

    /**
     * @return int
     */
    public function getConsumedQuantity() : int;

    /**
     * @return int
     */
    public function getRemainedQuantity() : int;
}
