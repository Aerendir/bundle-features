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

use SerendipityHQ\Bundle\FeaturesBundle\Property\HasUnatantumPricesInterface;
use SerendipityHQ\Bundle\FeaturesBundle\Property\HasUnatantumPricesProperty;

/**
 * RechargeableFeatures can be bought in packs.
 *
 * A Pack represents an amount of units of the ConfiguredRechargeableFeature with a corrispondent price.
 */
class ConfiguredRechargeableFeaturePack extends AbstractFeaturePack implements ConfiguredFeaturePackInterface, HasUnatantumPricesInterface
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
