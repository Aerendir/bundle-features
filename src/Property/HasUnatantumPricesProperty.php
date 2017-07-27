<?php

namespace SerendipityHQ\Bundle\FeaturesBundle\Property;

use SerendipityHQ\Bundle\FeaturesBundle\Model\ConfiguredRechargeableFeature;
use SerendipityHQ\Bundle\FeaturesBundle\Model\ConfiguredRechargeableFeatureInterface;
use SerendipityHQ\Bundle\FeaturesBundle\Model\ConfiguredRechargeableFeaturePack;
use SerendipityHQ\Component\ValueObjects\Currency\Currency;
use SerendipityHQ\Component\ValueObjects\Currency\CurrencyInterface;
use SerendipityHQ\Component\ValueObjects\Money\Money;
use SerendipityHQ\Component\ValueObjects\Money\MoneyInterface;

/**
 * Manages simple prices.
 *
 * Simple prices don't have a subscription interval and are a simple pair of currency => money value.
 */
trait HasUnatantumPricesProperty
{
    /** @var  array $grossPrices */
    private $grossPrices = [];

    /** @var  array $netPrices */
    private $netPrices = [];

    /** @var  string $pricesType */
    private $pricesType;

    /**
     * @param string|CurrencyInterface $currency This is not typecasted so the method can be called from inside Twig templates simply passing a string
     * @param string|null $type
     *
     * @return MoneyInterface|null if the price is not set in the required currency
     */
    public function getPrice($currency, string $type = null)
    {
        if ($currency instanceof CurrencyInterface) {
            $currency = $currency->getCurrencyCode();
        }

        if (null === $type) {
            $type = $this->pricesType;
        }

        return $this->getPrices($type)[$currency] ?? new Money(['amount' => 0, 'currency' => new Currency($currency)]);
    }

    /**
     * @param string|null $type
     * @return array
     */
    public function getPrices(string $type = null) : array
    {
        if (null === $type) {
            $type = $this->pricesType;
        }

        switch ($type) {
            case 'gross':
                return $this->grossPrices;
                break;
            case 'net':
                return $this->netPrices;
                break;
            default:
                throw new \InvalidArgumentException(sprintf('The prices can be only "net" or "gross". You asked for "%s" prices.', $type));
        }
    }

    /**
     * @param string|CurrencyInterface $currency This is not typecasted so the method can be called from inside Twig templates simply passing a string
     * @param string|null $type
     *
     * @return bool
     */
    public function hasPrice($currency, string $type = null): bool
    {
        if ($currency instanceof CurrencyInterface) {
            $currency = $currency->getCurrencyCode();
        }

        if (null === $type) {
            $type = $this->pricesType;
        }

        return isset($this->getPrices($type)[$currency]);
    }

    /**
     * @param float $rate
     * @return HasUnatantumPricesInterface
     */
    public function setTaxRate(float $rate) : HasUnatantumPricesInterface
    {
        $pricesProperty = 'net' === $this->pricesType ? 'netPrices' : 'grossPrices';
        // ... Then we have to set gross prices
        if (0 < count($this->$pricesProperty)) {
            /** @var MoneyInterface $price */
            foreach ($this->$pricesProperty as $currency => $price) {
                switch ($this->pricesType) {
                    // If currently is "net"...
                    case 'net':
                        $netPrice = (int) round($price->getAmount() * (1 + $rate));
                        $netPrice = new Money(['amount' => $netPrice, 'currency' => $currency]);
                        $this->grossPrices[$currency] = $netPrice;
                        break;
                    // If currently is "gross"...
                    case 'gross':
                        // ... Then we have to set net prices
                        $grossPrice = (int) round($price->getAmount() / (1 + $rate));
                        $grossPrice = new Money(['amount' => $grossPrice, 'currency' => $currency]);
                        $this->netPrices[$currency] = $grossPrice;
                        break;
                }
            }
        }

        /** @var $this HasUnatantumPricesInterface */
        return $this;
    }

    /**
     * @param array $prices
     * @param string $pricesType
     * @return ConfiguredRechargeableFeatureInterface|ConfiguredRechargeableFeaturePack
     */
    private function setPrices(array $prices, string $pricesType)
    {
        $this->pricesType = $pricesType;
        $priceProperty = $this->pricesType === 'net' ? 'netPrices' : 'grossPrices';

        foreach ($prices as $currency => $price) {
            $this->$priceProperty[$currency] = new Money(['amount' => $price, 'currency' => new Currency($currency)]);
        }

        /** @var ConfiguredRechargeableFeatureInterface|ConfiguredRechargeableFeaturePack $this */
        return $this;
    }
}