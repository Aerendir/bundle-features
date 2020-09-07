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

use SerendipityHQ\Bundle\FeaturesBundle\Model\Property\HasConfiguredPacksInterface;
use SerendipityHQ\Bundle\FeaturesBundle\Model\Property\HasConfiguredPacksProperty;
use SerendipityHQ\Bundle\FeaturesBundle\Model\Property\HasUnatantumPricesInterface;
use SerendipityHQ\Bundle\FeaturesBundle\Model\Property\HasUnatantumPricesProperty;

/**
 * {@inheritdoc}
 */
final class ConfiguredRechargeableFeature extends AbstractFeature implements ConfiguredRechargeableFeatureInterface
{
    use HasConfiguredPacksProperty {
        HasConfiguredPacksProperty::setPacks as setPacksProperty;
    }
    use HasUnatantumPricesProperty {
        HasUnatantumPricesProperty::setTax as setTaxProperty;
    }

    /** @var int $freeRecharge The amount of free units of this feature recharged each time */
    private $freeRecharge;

    /**
     * {@inheritdoc}
     */
    public function __construct(string $name, array $details = [])
    {
        // Set the type
        $details['type'] = self::RECHARGEABLE;

        $this->freeRecharge = $details['free_recharge'];

        if (isset($details['packs'])) {
            $this->setPacks($details['packs']);
        }

        if (isset($details['net_unitary_price'])) {
            $this->setPrices($details['net_unitary_price'], 'net');
        }

        if (isset($details['gross_unitary_price'])) {
            $this->setPrices($details['gross_unitary_price'], 'gross');
        }

        parent::__construct($name, $details);
    }

    /**
     * {@inheritdoc}
     */
    public function getFreeRecharge(): int
    {
        return $this->freeRecharge;
    }

    /**
     * {@inheritdoc}
     */
    public function setFreeRecharge(int $freeRecharge): ConfiguredRechargeableFeatureInterface
    {
        $this->freeRecharge = $freeRecharge;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setPacks(array $packs, string $class = null): HasConfiguredPacksInterface
    {
        return $this->setPacksProperty($packs, ConfiguredRechargeableFeaturePack::class);
    }

    public function setTax(float $rate, string $name): HasUnatantumPricesInterface
    {
        $this->setTaxProperty($rate, $name);

        /** @var ConfiguredRechargeableFeaturePack $pack Set tax rate in the packs too */
        foreach ($this->getPacks() as $pack) {
            $pack->setTax($rate, $name);
        }

        return $this;
    }
}
