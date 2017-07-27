<?php

namespace SerendipityHQ\Bundle\FeaturesBundle\Property;

use SerendipityHQ\Bundle\FeaturesBundle\Model\ConfiguredCountableFeaturePack;
use SerendipityHQ\Bundle\FeaturesBundle\Model\ConfiguredFeatureInterface;
use SerendipityHQ\Bundle\FeaturesBundle\Model\ConfiguredFeaturePackInterface;
use SerendipityHQ\Bundle\FeaturesBundle\Model\ConfiguredRechargeableFeaturePack;

/**
 * Implemented by Features that have packages and that can have free packs.
 */
interface CanHaveFreePackInterface
{
    /**
     * @return ConfiguredFeaturePackInterface
     */
    public function getFreePack() : ConfiguredFeaturePackInterface;

    /**
     * @return bool
     */
    public function hasFreePack() : bool;

    /**
     * @param ConfiguredFeaturePackInterface $pack
     * @return ConfiguredFeatureInterface
     */
    public function setFreePack(ConfiguredFeaturePackInterface $pack) : ConfiguredFeatureInterface;
}
