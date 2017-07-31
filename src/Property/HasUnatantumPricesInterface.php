<?php

namespace SerendipityHQ\Bundle\FeaturesBundle\Property;

use SerendipityHQ\Bundle\FeaturesBundle\Model\ConfiguredRechargeableFeatureInterface;
use SerendipityHQ\Bundle\FeaturesBundle\Model\ConfiguredRechargeableFeaturePack;
use SerendipityHQ\Component\ValueObjects\Currency\CurrencyInterface;
use SerendipityHQ\Component\ValueObjects\Money\MoneyInterface;

/**
 * Common methods to manage feature prices.
 */
interface HasUnatantumPricesInterface
{
    /**
     * @param string|CurrencyInterface $currency This is not typecasted so the method can be called from inside Twig templates simply passing a string
     * @param string|null $type
     *
     * @return MoneyInterface|null if the price is not set in the required currency
     */
    public function getPrice($currency, string $type = null);

    /**
     * @param string|null $type
     * @return array
     */
    public function getPrices(string $type = null) : array;

    /**
     * @return string
     */
    public function getTaxName() : string;

    /**
     * @return float
     */
    public function getTaxRate() : float;

    /**
     * @param string|CurrencyInterface $currency This is not typecasted so the method can be called from inside Twig templates simply passing a string
     * @param string|null $type
     *
     * @return bool
     */
    public function hasPrice($currency, string $type = null): bool;

    /**
     * @param float $rate
     * @param string $name
     * @return HasUnatantumPricesInterface
     */
    public function setTax(float $rate, string $name) : HasUnatantumPricesInterface;
}
