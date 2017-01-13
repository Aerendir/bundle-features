<?php

namespace SerendipityHQ\Bundle\FeaturesBundle\Model;

use SerendipityHQ\Bundle\FeaturesBundle\Property\CanBeFreeProperty;
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
    use CanBeFreeProperty;

    /**
     * @param int $numOfUnits
     * @param array $prices
     */
    public function __construct(int $numOfUnits, array $prices)
    {
        $this->setPrices($prices);

        parent::__construct(['num_of_units' => $numOfUnits]);
    }
}
