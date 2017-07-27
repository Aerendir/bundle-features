<?php

namespace SerendipityHQ\Bundle\FeaturesBundle\Model;

use SerendipityHQ\Bundle\FeaturesBundle\Property\CanBeFreeProperty;
use SerendipityHQ\Bundle\FeaturesBundle\Property\HasRecurringPricesInterface;
use SerendipityHQ\Bundle\FeaturesBundle\Property\HasRecurringPricesProperty;

/**
 * The subscribed pack of the SubscribedCountableFeature.
 */
class SubscribedFeaturePack extends AbstractFeaturePack implements SubscribedFeaturePackInterface
{
    /**
     * {@inheritdoc}
     */
    public function toArray() : array
    {
        return [
            'num_of_units' => $this->getNumOfUnits()
        ];
    }
}
