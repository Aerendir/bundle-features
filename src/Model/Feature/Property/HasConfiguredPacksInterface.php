<?php

declare(strict_types=1);

/*
 * This file is part of the Serendipity HQ Features Bundle.
 *
 * Copyright (c) Adamo Aerendir Crespi <aerendir@serendipityhq.com>.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SerendipityHQ\Bundle\FeaturesBundle\Model\Feature\Property;

use SerendipityHQ\Bundle\FeaturesBundle\Model\Feature\Configured\ConfiguredCountableFeaturePack;
use SerendipityHQ\Bundle\FeaturesBundle\Model\Feature\Configured\ConfiguredFeaturePackInterface;
use SerendipityHQ\Bundle\FeaturesBundle\Model\Feature\Configured\ConfiguredRechargeableFeaturePack;

/**
 * Implemented by Features that have packages.
 */
interface HasConfiguredPacksInterface
{
    public const _PRICES_TYPES = '_pricesType';

    /**
     * @return ConfiguredCountableFeaturePack|ConfiguredFeaturePackInterface|ConfiguredRechargeableFeaturePack|null
     */
    public function getPack(int $numOfUnits);

    /**
     * @return ConfiguredCountableFeaturePack[]|ConfiguredFeaturePackInterface[]|ConfiguredRechargeableFeaturePack[]
     */
    public function getPacks(): array;

    public function hasPack(int $numOfUnits): bool;

    /**
     * @param ConfiguredCountableFeaturePack[]|ConfiguredFeaturePackInterface[]|ConfiguredRechargeableFeaturePack[] $packs
     */
    public function setPacks(array $packs, string $packClass = null): HasConfiguredPacksInterface;
}
