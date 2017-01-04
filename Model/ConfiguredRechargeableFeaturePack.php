<?php

namespace SerendipityHQ\Bundle\FeaturesBundle\Model;

use SerendipityHQ\Bundle\FeaturesBundle\Property\SimplePricesProperty;

/**
 * RechargeableFeatures can be bought in packs.
 *
 * A Pack represents an amount of units of the ConfiguredConfiguredRechargeableFeature with a corrispondent price.
 */
class ConfiguredRechargeableFeaturePack
{
    use SimplePricesProperty;

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
