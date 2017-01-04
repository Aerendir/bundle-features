<?php

namespace SerendipityHQ\Bundle\FeaturesBundle\Property;

use SerendipityHQ\Bundle\FeaturesBundle\Model\RechargeableFeatureInterface;
use SerendipityHQ\Bundle\FeaturesBundle\Model\RechargeableFeaturePack;
use SerendipityHQ\Component\ValueObjects\Currency\Currency;
use SerendipityHQ\Component\ValueObjects\Currency\CurrencyInterface;
use SerendipityHQ\Component\ValueObjects\Money\Money;
use SerendipityHQ\Component\ValueObjects\Money\MoneyInterface;

/**
 * Manages simple prices.
 *
 * Simple prices don't have a subscription interval and are a simple pair of currency => money value.
 */
trait SimplePricesProperty
{
    /** @var  array $prices */
    private $prices = [];

    /**
     * @param string|CurrencyInterface $currency             This is not typecasted so the method can be called from inside Twig templates simply passing a string
     *
     * @return MoneyInterface|null if the price is not set in the required currency
     */
    public function getPrice($currency)
    {
        if ($currency instanceof CurrencyInterface) {
            $currency = $currency->getCurrencyCode();
        }

        return $this->getPrices()[$currency] ?? new Money(['amount' => 0, 'currency' => new Currency($currency)]);
    }

    /**
     * @return array
     */
    public function getPrices(): array
    {
        return $this->prices;
    }

    /**
     * @param CurrencyInterface|string $currency
     *
     * @return bool
     */
    public function hasPrice($currency): bool
    {
        if ($currency instanceof CurrencyInterface) {
            $currency = $currency->getCurrencyCode();
        }

        return isset($this->getPrices()[$currency]);
    }

    /**
     * @param array $prices
     * @return RechargeableFeatureInterface|RechargeableFeaturePack
     */
    public function setPrices(array $prices)
    {
        foreach ($prices as $currency => $price) {
            $this->prices[$currency] = new Money(['amount' => $price, 'currency' => new Currency($currency)]);
        }

        /** @var RechargeableFeatureInterface|RechargeableFeaturePack $this */
        return $this;
    }
}
