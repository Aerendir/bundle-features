<?php

/*
 * This file is part of the SHQFeaturesBundle.
 *
 * Copyright Adamo Aerendir Crespi 2016-2017.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    Adamo Aerendir Crespi <hello@aerendir.me>
 * @copyright Copyright (C) 2016 - 2017 Aerendir. All rights reserved.
 * @license   MIT License.
 */

namespace SerendipityHQ\Bundle\FeaturesBundle\Property;

use SerendipityHQ\Bundle\FeaturesBundle\Model\ConfiguredFeatureInterface;
use SerendipityHQ\Bundle\FeaturesBundle\Model\ConfiguredFeaturePackInterface;

/**
 * Concrete implementetion of the CanHaveFreePackInterface.
 */
trait CanHaveFreePackProperty
{
    /** @var ConfiguredFeaturePackInterface $freePack */
    private $freePack;

    /**
     * @return ConfiguredFeaturePackInterface
     */
    public function getFreePack(): ConfiguredFeaturePackInterface
    {
        return $this->freePack;
    }

    /**
     * @return bool
     */
    public function hasFreePack(): bool
    {
        return null !== $this->freePack;
    }

    /**
     * @param ConfiguredFeaturePackInterface $pack
     *
     * @return ConfiguredFeatureInterface
     */
    public function setFreePack(ConfiguredFeaturePackInterface $pack): ConfiguredFeatureInterface
    {
        $this->freePack = $pack;

        /** @var ConfiguredFeatureInterface $this */
        return $this;
    }
}
