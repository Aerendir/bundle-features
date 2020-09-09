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

use SerendipityHQ\Bundle\FeaturesBundle\Model\Feature\AbstractFeaturePack;
use SerendipityHQ\Bundle\FeaturesBundle\Model\Feature\FeaturePackInterface;
use SerendipityHQ\Bundle\FeaturesBundle\Model\Feature\Property\HasUnatantumPricesInterface;
use SerendipityHQ\Bundle\FeaturesBundle\Model\Feature\Property\HasUnatantumPricesProperty;

/**
 * RechargeableFeatures can be bought in packs.
 *
 * A Pack represents an amount of units of the ConfiguredRechargeableFeature with a corrispondent price.
 */
final class ConfiguredRechargeableFeaturePack extends AbstractFeaturePack implements ConfiguredFeaturePackInterface, HasUnatantumPricesInterface
{
    use HasUnatantumPricesProperty;

    public function __construct(int $numOfUnits, array $prices, string $pricesType)
    {
        $this->setPrices($prices, $pricesType);

        parent::__construct([FeaturePackInterface::FIELD_NUM_OF_UNITS => $numOfUnits]);
    }
}
