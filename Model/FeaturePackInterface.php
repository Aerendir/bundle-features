<?php

namespace SerendipityHQ\Bundle\FeaturesBundle\Model;

/**
 * Common interface for all type of feature packages.
 */
interface FeaturePackInterface
{
    /**
     * @return int
     */
    public function getNumOfUnits() : int;
}
