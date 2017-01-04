<?php

namespace SerendipityHQ\Bundle\FeaturesBundle\Property;

use SerendipityHQ\Bundle\FeaturesBundle\Model\RechargeableFeatureInterface;
use SerendipityHQ\Bundle\FeaturesBundle\Model\RechargeableFeaturePack;
use SerendipityHQ\Component\ValueObjects\Currency\CurrencyInterface;
use SerendipityHQ\Component\ValueObjects\Money\MoneyInterface;

/**
 * Common methods of a SimplePrices object.
 */
interface SimplePricesInterface
{
    /**
     * @param string|CurrencyInterface $currency             This is not typecasted so the method can be called from inside Twig templates simply passing a string
     *
     * @return MoneyInterface|null if the price is not set in the required currency
     */
    public function getPrice($currency);

    /**
     * @return array
     */
    public function getPrices() : array;

    /**
     * @param CurrencyInterface|string $currency
     *
     * @return bool
     */
    public function hasPrice($currency) : bool;

    /**
     * @param array $prices
     * @return RechargeableFeatureInterface|RechargeableFeaturePack
     */
    public function setPrices(array $prices);
}
