<?php

namespace SerendipityHQ\Bundle\FeaturesBundle\Property;

use SerendipityHQ\Bundle\FeaturesBundle\Model\ConfiguredRechargeableFeatureInterface;
use SerendipityHQ\Bundle\FeaturesBundle\Model\ConfiguredRechargeableFeaturePack;
use SerendipityHQ\Component\ValueObjects\Currency\CurrencyInterface;
use SerendipityHQ\Component\ValueObjects\Money\MoneyInterface;

/**
 * Common methods to manage feature prices.
 */
interface HasUnatantumPricesInterface extends CanBeFreeInterface
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
     * @return ConfiguredRechargeableFeatureInterface|ConfiguredRechargeableFeaturePack
     */
    public function setPrices(array $prices);
}
