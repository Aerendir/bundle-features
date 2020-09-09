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
use SerendipityHQ\Bundle\FeaturesBundle\Model\Feature\Property\HasConfiguredPacksInterface;
use SerendipityHQ\Bundle\FeaturesBundle\Model\Feature\Property\HasConfiguredPacksProperty;
use SerendipityHQ\Bundle\FeaturesBundle\Model\Feature\Property\HasUnatantumPricesInterface;
use SerendipityHQ\Bundle\FeaturesBundle\Model\Feature\Property\HasUnatantumPricesProperty;

final class ConfiguredRechargeableFeature extends AbstractFeature implements ConfiguredFeatureInterface, HasUnatantumPricesInterface, HasConfiguredPacksInterface
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
        $details[FeatureInterface::FIELD_TYPE] = self::TYPE_RECHARGEABLE;

        $this->freeRecharge = $details['free_recharge'];

        if (isset($details['packs'])) {
            $this->setPacks($details['packs']);
        }

        if (isset($details['net_unitary_price'])) {
            $this->setPrices($details['net_unitary_price'], FeatureInterface::PRICE_NET);
        }

        if (isset($details['gross_unitary_price'])) {
            $this->setPrices($details['gross_unitary_price'], FeatureInterface::PRICE_GROSS);
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
    public function setFreeRecharge(int $freeRecharge): ConfiguredRechargeableFeature
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
