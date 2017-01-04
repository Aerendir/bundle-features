<?php

namespace SerendipityHQ\Bundle\FeaturesBundle\Model;

/**
 * {@inheritdoc}
 */
interface SubscribedCountableFeatureInterface extends SubscribedFeatureInterface, SubscribedRecurringFeatureInterface
{
    /**
     * @return int
     */
    public function getFreeAmount() : int;
}
