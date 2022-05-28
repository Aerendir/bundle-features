<?php

/*
 * This file is part of the Serendipity HQ Features Bundle.
 *
 * Copyright (c) Adamo Aerendir Crespi <aerendir@serendipityhq.com>.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SerendipityHQ\Bundle\FeaturesBundle\Model\Feature\Configured;

use SerendipityHQ\Bundle\FeaturesBundle\Model\Feature\AbstractFeature;
use SerendipityHQ\Bundle\FeaturesBundle\Model\Feature\FeatureInterface;
use SerendipityHQ\Bundle\FeaturesBundle\Model\Feature\Property\CanHaveFreePackInterface;
use SerendipityHQ\Bundle\FeaturesBundle\Model\Feature\Property\CanHaveFreePackProperty;
use SerendipityHQ\Bundle\FeaturesBundle\Model\Feature\Property\HasConfiguredPacksInterface;
use SerendipityHQ\Bundle\FeaturesBundle\Model\Feature\Property\HasConfiguredPacksProperty;
use SerendipityHQ\Bundle\FeaturesBundle\Model\SubscriptionInterface;

final class ConfiguredCountableFeature extends AbstractFeature implements HasConfiguredPacksInterface, CanHaveFreePackInterface, ConfiguredFeatureInterface
{
    use HasConfiguredPacksProperty {
        HasConfiguredPacksProperty::setPacks as setPacksProperty;
    }
    use CanHaveFreePackProperty;

    /** @var array $packs */
    private $packs;

    /** @var string $refreshPeriod */
    private $refreshPeriod;

    private string $taxName;

    private float $taxRate;

    public function __construct(string $name, array $details = [])
    {
        // Set the type
        $details[FeatureInterface::FIELD_TYPE] = self::TYPE_COUNTABLE;

        if (isset($details['packs'])) {
            $this->setPacks($details['packs']);
        }

        $this->refreshPeriod = $details['refresh_period'];

        parent::__construct($name, $details);
    }

    public function getRefreshPeriod(): string
    {
        return $this->refreshPeriod;
    }

    public function setPacks(array $packs, string $class = null): HasConfiguredPacksInterface
    {
        return $this->setPacksProperty($packs, ConfiguredCountableFeaturePack::class);
    }

    public function setSubscription(SubscriptionInterface $subscription): self
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

    public function setTax(float $rate, string $name): self
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
