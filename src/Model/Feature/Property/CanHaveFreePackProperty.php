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
 * Concrete implementetion of the CanHaveFreePackInterface.
 */
trait CanHaveFreePackProperty
{
    /** @var ConfiguredFeaturePackInterface $freePack */
    private $freePack;

    public function getFreePack(): ConfiguredFeaturePackInterface
    {
        return $this->freePack;
    }

    public function hasFreePack(): bool
    {
        return null !== $this->freePack;
    }

    public function setFreePack(ConfiguredFeaturePackInterface $pack): ConfiguredFeatureInterface
    {
        $this->freePack = $pack;

        /** @var ConfiguredFeatureInterface $this */
        return $this;
    }
}
