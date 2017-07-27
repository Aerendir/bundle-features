<?php

namespace SerendipityHQ\Bundle\FeaturesBundle\Model;

/**
 * Common interface for Subscribed Features packages.
 */
interface SubscribedFeaturePackInterface extends FeaturePackInterface
{
    /**
     * @return array
     */
    public function toArray() : array;
}
