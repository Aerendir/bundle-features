<?php

/*
 * This file is part of the SHQFeaturesBundle.
 *
 * Copyright Adamo Aerendir Crespi 2016-2017.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    Adamo Aerendir Crespi <hello@aerendir.me>
 * @copyright Copyright (C) 2016 - 2017 Aerendir. All rights reserved.
 * @license   MIT License.
 */

namespace SerendipityHQ\Bundle\FeaturesBundle\Property;

use Money\Currency;
use SerendipityHQ\Bundle\FeaturesBundle\Model\ConfiguredRechargeableFeatureInterface;
use SerendipityHQ\Bundle\FeaturesBundle\Model\ConfiguredRechargeableFeaturePack;
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
     * @param string|null     $type
     *
     * @return MoneyInterface|null if the price is not set in the required currency
     */
    public function getPrice($currency, string $type = null)
    {
        if ($currency instanceof Currency) {
            $currency = $currency->getCode();
        }

        if (null === $type) {
            $type = $this->pricesType;
        }

        return $this->getPrices($type)[$currency] ?? new Money(['baseAmount' => 0, 'currency' => new Currency($currency)]);
    }

    /**
     * @return string
     */
    public function getTaxName(): string
    {
        return $this->taxName;
    }

    /**
     * @return float
     */
    public function getTaxRate(): float
    {
        return $this->taxRate;
    }

    /**
     * @param string|null $type
     *
     * @return array
     */
    public function getPrices(string $type = null): array
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
     * @param Currency|string $currency This is not typecasted so the method can be called from inside Twig templates simply passing a string
     * @param string|null     $type
     *
     * @return bool
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

    /**
     * @param float  $rate
     * @param string $name
     *
     * @return HasUnatantumPricesInterface
     */
    public function setTax(float $rate, string $name): HasUnatantumPricesInterface
    {
        $this->taxName = $name;
        $this->taxRate = $rate;

        $pricesProperty = 'net' === $this->pricesType ? 'netPrices' : 'grossPrices';
        // ... Then we have to set gross prices
        if (0 < count($this->$pricesProperty)) {
            /** @var MoneyInterface $price */
            foreach ($this->$pricesProperty as $currency => $price) {
                switch ($this->pricesType) {
                    // If currently is "net"...
                    case 'net':
                        $netPrice                     = (int) round($price->getAmount() * (1 + $rate));
                        $netPrice                     = new Money(['baseAmount' => $netPrice, 'currency' => $currency]);
                        $this->grossPrices[$currency] = $netPrice;
                        break;
                    // If currently is "gross"...
                    case 'gross':
                        // ... Then we have to set net prices
                        $grossPrice                 = (int) round($price->getAmount() / (1 + $rate));
                        $grossPrice                 = new Money(['baseAmount' => $grossPrice, 'currency' => $currency]);
                        $this->netPrices[$currency] = $grossPrice;
                        break;
                }
            }
        }

        /** @var $this HasUnatantumPricesInterface */
        return $this;
    }

    /**
     * @param array  $prices
     * @param string $pricesType
     *
     * @return ConfiguredRechargeableFeatureInterface|ConfiguredRechargeableFeaturePack
     */
    private function setPrices(array $prices, string $pricesType)
    {
        $this->pricesType = $pricesType;
        $priceProperty    = 'net' === $this->pricesType ? 'netPrices' : 'grossPrices';

        foreach ($prices as $currency => $price) {
            $this->$priceProperty[$currency] = new Money(['baseAmount' => $price, 'currency' => new Currency($currency)]);
        }

        /** @var ConfiguredRechargeableFeatureInterface|ConfiguredRechargeableFeaturePack $this */
        return $this;
    }
}
