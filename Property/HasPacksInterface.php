<?php

namespace SerendipityHQ\Bundle\FeaturesBundle\Property;

use SerendipityHQ\Bundle\FeaturesBundle\Model\ConfiguredFeaturePackInterface;

/**
 * Implemented by Features that have packages.
 */
interface HasPacksInterface
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
     * @param array $packs
     * @param string|null $packClass
     * @return HasPacksInterface
     */
    public function setPacks(array $packs, string $packClass = null) : HasPacksInterface;
}
