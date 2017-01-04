<?php

namespace SerendipityHQ\Bundle\FeaturesBundle\Model;

/**
 * A Feature loaded from a subscription.
 */
interface SubscribedFeatureInterface extends FeatureInterface
{
    /**
     * Converts a Feature object into an array.
     *
     * @return array
     */
    public function toArray();
}
