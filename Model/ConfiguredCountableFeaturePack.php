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
class ConfiguredCountableFeaturePack implements ConfiguredFeaturePackInterface, HasRecurringPricesInterface
{
    use HasRecurringPricesProperty;
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

    public function getNumOfUnits() : int
    {
        return $this->numOfUnits;
    }
}
