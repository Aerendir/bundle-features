<?php

/*
 * This file is part of the Serendipity HQ Features Bundle.
 *
 * Copyright (c) Adamo Aerendir Crespi <aerendir@serendipityhq.com>.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SerendipityHQ\Bundle\FeaturesBundle\Model;

use SerendipityHQ\Bundle\FeaturesBundle\Model\Property\CanHaveFreePackProperty;
use SerendipityHQ\Bundle\FeaturesBundle\Model\Property\HasConfiguredPacksInterface;
use SerendipityHQ\Bundle\FeaturesBundle\Model\Property\HasConfiguredPacksProperty;

/**
 * {@inheritdoc}
 */
final class ConfiguredCountableFeature extends AbstractFeature implements ConfiguredCountableFeatureInterface
{
    use HasConfiguredPacksProperty {
        HasConfiguredPacksProperty::setPacks as setPacksProperty;
    }
    use CanHaveFreePackProperty;

    /** @var array $packs */
    private $packs;

    /** @var string $refreshPeriod */
    private $refreshPeriod;

    /** @var string $taxName */
    private $taxName;

    /** @var float $taxRate */
    private $taxRate;

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
        $this->taxRate = $rate;
        $this->taxName = $name;

        /** @var ConfiguredCountableFeaturePack $pack Set tax rate in the packs too */
        foreach ($this->getPacks() as $pack) {
            $pack->setTax($rate, $name);
        }

        return $this;
    }

    public function getTaxName(): string
    {
        return $this->taxName;
    }

    public function getTaxRate(): float
    {
        return $this->taxRate;
    }
}
