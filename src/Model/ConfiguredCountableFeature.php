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

namespace SerendipityHQ\Bundle\FeaturesBundle\Model;

use SerendipityHQ\Bundle\FeaturesBundle\Property\CanHaveFreePackProperty;
use SerendipityHQ\Bundle\FeaturesBundle\Property\HasConfiguredPacksInterface;
use SerendipityHQ\Bundle\FeaturesBundle\Property\HasConfiguredPacksProperty;

/**
 * {@inheritdoc}
 */
class ConfiguredCountableFeature extends AbstractFeature implements ConfiguredCountableFeatureInterface
{
    use HasConfiguredPacksProperty {
        HasConfiguredPacksProperty::setPacks as setPacksProperty;
    }
    use CanHaveFreePackProperty;

    /** @var array $packs */
    private $packs;

    /** @var string $refreshPeriod */
    private $refreshPeriod;

    /**
     * {@inheritdoc}
     */
    public function __construct(string $name, array $details = [])
    {
        // Set the type
        $details['type'] = self::COUNTABLE;

        if (isset($details['packs'])) {
            $this->setPacks($details['packs']);
        }

        $this->refreshPeriod = $details['refresh_period'];

        parent::__construct($name, $details);
    }

    /**
     * {@inheritdoc}
     */
    public function getRefreshPeriod(): string
    {
        return $this->refreshPeriod;
    }

    /**
     * {@inheritdoc}
     */
    public function setPacks(array $packs, string $class = null): HasConfiguredPacksInterface
    {
        return $this->setPacksProperty($packs, ConfiguredCountableFeaturePack::class);
    }

    /**
     * {@inheritdoc}
     */
    public function setSubscription(SubscriptionInterface $subscription): ConfiguredCountableFeatureInterface
    {
        // If there are packs, set subscription in them, too
        if (false === empty($this->packs)) {
            /** @var ConfiguredCountableFeaturePack $pack */
            foreach ($this->packs as $pack) {
                $pack->setSubscription($subscription);
            }
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setTax(float $rate, string $name): ConfiguredCountableFeatureInterface
    {
        /** @var ConfiguredCountableFeaturePack $pack Set tax rate in the packs too */
        foreach ($this->getPacks() as $pack) {
            $pack->setTax($rate, $name);
        }

        return $this;
    }
}
