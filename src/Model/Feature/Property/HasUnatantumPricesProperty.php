<?php

/*
 * This file is part of the Serendipity HQ Features Bundle.
 *
 * Copyright (c) Adamo Aerendir Crespi <aerendir@serendipityhq.com>.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SerendipityHQ\Bundle\FeaturesBundle\Model\Feature\Property;

use Money\Currency;
use SerendipityHQ\Bundle\FeaturesBundle\Model\Feature\Configured\ConfiguredRechargeableFeature;
use SerendipityHQ\Bundle\FeaturesBundle\Model\Feature\Configured\ConfiguredRechargeableFeaturePack;
use SerendipityHQ\Bundle\FeaturesBundle\Model\Feature\FeatureInterface;
use SerendipityHQ\Component\ValueObjects\Money\Money;
use SerendipityHQ\Component\ValueObjects\Money\MoneyInterface;

/**
 * Manages simple prices.
 *
 * Simple prices don't have a subscription interval and are a simple pair of currency => money value.
 */
trait HasUnatantumPricesProperty
{
    /** @var array $grossPrices */
    private $grossPrices = [];

    /** @var array $netPrices */
    private $netPrices = [];

    /** @var string $pricesType */
    private $pricesType;

    /** @var string $taxName */
    private $taxName;

    /** @var float $taxRate */
    private $taxRate;

    /**
     * @param Currency|string $currency This is not typecasted so the method can be called from inside Twig templates simply passing a string
     *
     * @return MoneyInterface|null if the price is not set in the required currency
     */
    public function getPrice($currency, string $type = null): ?MoneyInterface
    {
        if ($currency instanceof Currency) {
            $currency = $currency->getCode();
        }

        if (null === $type) {
            $type = $this->pricesType;
        }

        return $this->getPrices($type)[$currency] ?? new Money([MoneyInterface::BASE_AMOUNT => 0, MoneyInterface::CURRENCY => new Currency($currency)]);
    }

    public function getTaxName(): string
    {
        return $this->taxName;
    }

    public function getTaxRate(): float
    {
        return $this->taxRate;
    }

    public function getPrices(string $type = null): array
    {
        if (null === $type) {
            $type = $this->pricesType;
        }

        switch ($type) {
            case FeatureInterface::PRICE_GROSS:
                return $this->grossPrices;
            case FeatureInterface::PRICE_NET:
                return $this->netPrices;
            default:
                throw new \InvalidArgumentException(\Safe\sprintf('The prices can be only "net" or "gross". You asked for "%s" prices.', $type));
        }
    }

    /**
     * @param Currency|string $currency This is not typecasted so the method can be called from inside Twig templates simply passing a string
     */
    public function hasPrice($currency, string $type = null): bool
    {
        if ($currency instanceof Currency) {
            $currency = $currency->getCode();
        }

        if (null === $type) {
            $type = $this->pricesType;
        }

        return isset($this->getPrices($type)[$currency]);
    }

    public function setTax(float $rate, string $name): HasUnatantumPricesInterface
    {
        $this->taxName = $name;
        $this->taxRate = $rate;

        $pricesProperty = FeatureInterface::PRICE_NET === $this->pricesType ? 'netPrices' : 'grossPrices';
        // ... Then we have to set gross prices
        if (0 < \count($this->$pricesProperty)) {
            /** @var MoneyInterface $price */
            foreach ($this->$pricesProperty as $currency => $price) {
                switch ($this->pricesType) {
                    // If currently is "net"...
                    case FeatureInterface::PRICE_NET:
                        $netPrice                     = (int) \round($price->getBaseAmount() * (1 + $rate));
                        $netPrice                     = new Money([MoneyInterface::BASE_AMOUNT => $netPrice, MoneyInterface::CURRENCY => $currency]);
                        $this->grossPrices[$currency] = $netPrice;
                        break;
                    // If currently is "gross"...
                    case FeatureInterface::PRICE_GROSS:
                        // ... Then we have to set net prices
                        $grossPrice                 = (int) \round($price->getBaseAmount() / (1 + $rate));
                        $grossPrice                 = new Money([MoneyInterface::BASE_AMOUNT => $grossPrice, MoneyInterface::CURRENCY => $currency]);
                        $this->netPrices[$currency] = $grossPrice;
                        break;
                }
            }
        }

        /** @var $this HasUnatantumPricesInterface */
        return $this;
    }

    /**
     * @return ConfiguredRechargeableFeature|ConfiguredRechargeableFeaturePack
     */
    private function setPrices(array $prices, string $pricesType)
    {
        $this->pricesType = $pricesType;
        $priceProperty    = FeatureInterface::PRICE_NET === $this->pricesType ? 'netPrices' : 'grossPrices';

        foreach ($prices as $currency => $price) {
            $this->$priceProperty[$currency] = new Money([MoneyInterface::BASE_AMOUNT => $price, MoneyInterface::CURRENCY => new Currency($currency)]);
        }

        /** @var ConfiguredRechargeableFeature|ConfiguredRechargeableFeaturePack $this */
        return $this;
    }
}
