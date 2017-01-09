<?php

namespace SerendipityHQ\Bundle\FeaturesBundle\Property;

use SerendipityHQ\Bundle\FeaturesBundle\Model\ConfiguredFeaturePackInterface;

/**
 * Implemented by Features that have packages.
 */
interface HasConfiguredPacksInterface
{
    /**
     * @return ConfiguredFeaturePackInterface
     */
    public function getFreePack() : ConfiguredFeaturePackInterface;

    /**
     * @param int $numOfUnits
     * @return null|ConfiguredFeaturePackInterface
     */
    public function getPack(int $numOfUnits);

    /**
     * @return array
     */
    public function getPacks() : array;

    /**
     * @return bool
     */
    public function hasFreePack() : bool;

    /**
     * @param int $numOfUnits
     * @return bool
     */
    public function hasPack(int $numOfUnits) : bool;

    /**
     * @param array $packs
     * @param string|null $packClass
     * @return HasConfiguredPacksInterface
     */
    public function setPacks(array $packs, string $packClass = null) : HasConfiguredPacksInterface;
}
