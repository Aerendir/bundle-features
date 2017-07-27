<?php

namespace SerendipityHQ\Bundle\FeaturesBundle\Model;

use SerendipityHQ\Bundle\FeaturesBundle\Property\CanBeFreeProperty;
use SerendipityHQ\Bundle\FeaturesBundle\Property\HasRecurringPricesInterface;
use SerendipityHQ\Bundle\FeaturesBundle\Property\HasRecurringPricesProperty;

/**
 * CountableFeatures can be bought in packs on each subscription period.
 *
 * A Pack represents an amount of units of the ConfiguredCountableFeature with a corrispondent price.
 */
class ConfiguredCountableFeaturePack extends AbstractFeaturePack implements ConfiguredFeaturePackInterface, HasRecurringPricesInterface
{
    use HasRecurringPricesProperty;
    use CanBeFreeProperty;

    /**
     * @param int $numOfUnits
     * @param array $prices
     * @param string $pricesType
     */
    public function __construct(int $numOfUnits, array $prices, string $pricesType)
    {
        $this->setPrices($prices, $pricesType);

        parent::__construct(['num_of_units' => $numOfUnits]);
    }
}
