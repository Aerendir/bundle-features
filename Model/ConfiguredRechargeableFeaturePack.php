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
class ConfiguredRechargeableFeaturePack implements ConfiguredFeaturePackInterface, HasUnatantumPricesInterface
{
    use HasUnatantumPricesProperty;
    use CanBeFreeProperty;

    /** @var  int $numOfUnits How many units are contained in this Pack */
    private $numOfUnits;

    /**
     * @param int $numOfUnits
     * @param array $prices
     */
    public function __construct(int $numOfUnits, array $prices)
    {
        $this->numOfUnits = $numOfUnits;
        $this->setPrices($prices);
    }
}
