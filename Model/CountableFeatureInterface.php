<?php

namespace SerendipityHQ\Bundle\FeaturesBundle\Model;

/**
 * {@inheritdoc}
 */
interface CountableFeatureInterface extends RecurringFeatureInterface
{
    /**
     * @return int
     */
    public function getFreeAmount() : int;
}
