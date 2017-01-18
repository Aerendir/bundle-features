<?php

namespace SerendipityHQ\Bundle\FeaturesBundle\Property;

use SerendipityHQ\Bundle\FeaturesBundle\Model\ConfiguredFeatureInterface;
use SerendipityHQ\Bundle\FeaturesBundle\Model\ConfiguredFeaturePackInterface;

/**
 * Concrete implementetion of the CanHaveFreePackInterface.
 */
trait CanHaveFreePackProperty
{
    /** @var  ConfiguredFeaturePackInterface $freePack */
    private $freePack;

    /**
     * @return ConfiguredFeaturePackInterface
     */
    public function getFreePack() : ConfiguredFeaturePackInterface
    {
        return $this->freePack;
    }

    /**
     * @return bool
     */
    public function hasFreePack() : bool
    {
        return null !== $this->freePack;
    }

    /**
     * @param ConfiguredFeaturePackInterface $pack
     * @return ConfiguredFeatureInterface
     */
    public function setFreePack(ConfiguredFeaturePackInterface $pack) : ConfiguredFeatureInterface
    {
        $this->freePack = $pack;

        /** @var ConfiguredFeatureInterface $this */
        return $this;
    }
}
