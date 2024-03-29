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

use SerendipityHQ\Bundle\FeaturesBundle\Model\Feature\Configured\ConfiguredFeatureInterface;
use SerendipityHQ\Bundle\FeaturesBundle\Model\Feature\Configured\ConfiguredFeaturePackInterface;

/**
 * Implemented by Features that have packages and that can have free packs.
 */
interface CanHaveFreePackInterface
{
    public function getFreePack(): ConfiguredFeaturePackInterface;

    public function hasFreePack(): bool;

    public function setFreePack(ConfiguredFeaturePackInterface $pack): ConfiguredFeatureInterface;
}
