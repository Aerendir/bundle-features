<?php

namespace SerendipityHQ\Bundle\FeaturesBundle\Model;

/**
 * {@inheritdoc}
 */
abstract class AbstractFeaturePack implements FeaturePackInterface
{
    /** @var  int $numOfUnits How many units are contained in this Pack */
    private $numOfUnits;

    /**
     * @param array $details
     */
    public function __construct(array $details)
    {
        $this->numOfUnits = $details['num_of_units'];
    }

    /**
     * {@inheritdoc}
     */
    public function getNumOfUnits() : int
    {
        return $this->numOfUnits;
    }
}
