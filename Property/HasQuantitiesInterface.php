<?php

namespace SerendipityHQ\Bundle\FeaturesBundle\Property;

/**
 * Common method for Features that keep track of quantities.
 */
interface HasQuantitiesInterface
{
    /**
     * @return int
     */
    public function getRemainedQuantity() : int;

    /**
     * @return int
     */
    public function getInitialQuantity() : int;
}
