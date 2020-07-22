<?php

/*
 * This file is part of the Serendipity HQ Features Bundle.
 *
 * Copyright (c) Adamo Aerendir Crespi <aerendir@serendipityhq.com>.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SerendipityHQ\Bundle\FeaturesBundle\Property;

use SerendipityHQ\Bundle\FeaturesBundle\Model\ConfiguredCountableFeaturePack;
use SerendipityHQ\Bundle\FeaturesBundle\Model\ConfiguredFeaturePackInterface;
use SerendipityHQ\Bundle\FeaturesBundle\Model\ConfiguredRechargeableFeaturePack;

/**
 * Implemented by Features that have packages.
 */
interface HasConfiguredPacksInterface
{
    /**
     * @param int $numOfUnits
     *
     * @return ConfiguredCountableFeaturePack|ConfiguredFeaturePackInterface|ConfiguredRechargeableFeaturePack|null
     */
    public function getPack(int $numOfUnits);

    /**
     * @return array
     */
    public function getPacks(): array;

    /**
     * @param int $numOfUnits
     *
     * @return bool
     */
    public function hasPack(int $numOfUnits): bool;

    /**
     * @param array       $packs
     * @param string|null $packClass
     *
     * @return HasConfiguredPacksInterface
     */
    public function setPacks(array $packs, string $packClass = null): HasConfiguredPacksInterface;
}
