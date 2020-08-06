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

use SerendipityHQ\Bundle\FeaturesBundle\Property\HasUnatantumPricesInterface;
use SerendipityHQ\Bundle\FeaturesBundle\Property\HasUnatantumPricesProperty;

/**
 * RechargeableFeatures can be bought in packs.
 *
 * A Pack represents an amount of units of the ConfiguredRechargeableFeature with a corrispondent price.
 */
final class ConfiguredRechargeableFeaturePack extends AbstractFeaturePack implements ConfiguredFeaturePackInterface, HasUnatantumPricesInterface
{
    use HasUnatantumPricesProperty;

    /**
     * @param int    $numOfUnits
     * @param array  $prices
     * @param string $pricesType
     */
    public function __construct(int $numOfUnits, array $prices, string $pricesType)
    {
        $this->setPrices($prices, $pricesType);

        parent::__construct(['num_of_units' => $numOfUnits]);
    }
}
