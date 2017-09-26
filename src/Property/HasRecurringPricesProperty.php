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
use SerendipityHQ\Bundle\FeaturesBundle\Model\Subscription;
use SerendipityHQ\Bundle\FeaturesBundle\Model\SubscriptionInterface;
use SerendipityHQ\Component\ValueObjects\Money\Money;
use SerendipityHQ\Component\ValueObjects\Money\MoneyInterface;

/**
 * Common properties and methods of a ConfiguredRecurringFeatureInterface.
 *
 * @method isFromConfiguration() Is contained in AbstractFeature
 */
trait HasRecurringPricesProperty
{
    /** @var array $instantGrossPrices */
    private $instantGrossPrices = [];

    /** @var array $instantNetPrices */
    private $instantNetPrices = [];

    /** @var array $grossPrices */
    private $grossPrices = [];

    /** @var array $netPrices */
    private $netPrices = [];

    /** @var string $pricesType */
    private $pricesType;

    /** @var SubscriptionInterface $subscription */
    private $subscription;

    /** @var string $taxName */
    private $taxName;

    /** @var float $taxRate */
    private $taxRate;

    /**
     * @param array $details
     */
    public function __construct(array $details = [])
    {
        if (isset($details['net_prices'])) {
            $this->setPrices($details['net_prices'], 'net');
        }

        if (isset($details['gross_prices'])) {
            $this->setPrices($details['gross_prices'], 'gross');
        }

        /* Not required anymore
        if ($this instanceof SubscribedFeatureInterface && !$this instanceof IsRecurringFeatureInterface) {
            throw new \LogicException('To have recurring prices, a Feature MUST implement IsRecurringFeatureInterface.');
        }
        */
    }

    /**
     * @param Currency|string $currency             This is not typecasted so the method can be called from inside Twig templates simply passing a string
     * @param string          $subscriptionInterval
     * @param string|null     $type
     *
     * @throws \InvalidArgumentException If the $subscriptionInterval does not exist
     *
     * @return MoneyInterface|null if the price is not set in the required currency
     */
    public function getInstantPrice($currency, string $subscriptionInterval, string $type = null): MoneyInterface
    {
        if ($currency instanceof Currency) {
            $currency = $currency->getCode();
        }

        if (null === $type) {
            $type = $this->pricesType;
        }

        $instantPricesProperty = 'net' === $type ? 'instantNetPrice' : 'instantGrossPrice';

        if (false === isset($this->$instantPricesProperty[$currency][$subscriptionInterval])) {
            $this->$instantPricesProperty[$currency][$subscriptionInterval] = $this->calculateInstantPrice($currency, $subscriptionInterval, $type);
        }

        return $this->$instantPricesProperty[$currency][$subscriptionInterval] ?? null;
    }

    /**
     * @param Currency|string $currency             This is not typecasted so the method can be called from inside Twig templates simply passing a string
     * @param string          $subscriptionInterval
     * @param string|null     $type
     *
     * @return MoneyInterface
     */
    public function getPrice($currency, string $subscriptionInterval, string $type = null): MoneyInterface
    {
        if ($currency instanceof Currency) {
            $currency = $currency->getCode();
        }

        Subscription::checkIntervalExists($subscriptionInterval);

        if (null === $type) {
            $type = $this->pricesType;
        }

        return $this->getPrices($type)[$currency][$subscriptionInterval] ?? new Money(['baseAmount' => 0, 'currency' => new Currency($currency)]);
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
     * @param Currency|string $currency             This is not typecasted so the method can be called from inside Twig templates simply passing a string
     * @param string          $subscriptionInterval
     * @param string|null     $type
     *
     * @return bool
     */
    public function hasPrice($currency, string $subscriptionInterval, string $type = null): bool
    {
        Subscription::checkIntervalExists($subscriptionInterval);

        if ($currency instanceof Currency) {
            $currency = $currency->getCode();
        }

        if (null === $type) {
            $type = $this->pricesType;
        }

        return isset($this->getPrices($type)[$currency][$subscriptionInterval]);
    }

    /**
     * @param SubscriptionInterface $subscription
     *
     * @return HasRecurringPricesInterface
     */
    public function setSubscription(SubscriptionInterface $subscription): HasRecurringPricesInterface
    {
        $this->subscription = $subscription;

        /** @var HasRecurringPricesInterface $this */
        return $this;
    }

    /**
     * @param float  $rate
     * @param string $name
     *
     * @return HasRecurringPricesInterface
     */
    public function setTax(float $rate, string $name): HasRecurringPricesInterface
    {
        $this->taxName = $name;
        $this->taxRate = $rate;

        $pricesProperty = 'net' === $this->pricesType ? 'netPrices' : 'grossPrices';
        // ... Then we have to set gross prices
        if (0 < count($this->$pricesProperty)) {
            foreach ($this->$pricesProperty as $currency => $prices) {
                /** @var MoneyInterface $price */
                foreach ($prices as $subscriptionInterval => $price) {
                    switch ($this->pricesType) {
                        // If currently is "net"...
                        case 'net':
                            $netPrice                                            = (int) round($price->getBaseAmount() * (1 + $rate));
                            $netPrice                                            = new Money(['baseAmount' => $netPrice, 'currency' => $currency]);
                            $this->grossPrices[$currency][$subscriptionInterval] = $netPrice;
                            break;
                        // If currently is "gross"...
                        case 'gross':
                            // ... Then we have to set net prices
                            $grossPrice                                        = (int) round($price->getBaseAmount() / (1 + $rate));
                            $grossPrice                                        = new Money(['baseAmount' => $grossPrice, 'currency' => $currency]);
                            $this->netPrices[$currency][$subscriptionInterval] = $grossPrice;
                            break;
                    }
                }
            }
        }

        /** @var HasRecurringPricesInterface $this */
        return $this;
    }

    /**
     * @param array  $prices
     * @param string $pricesType
     */
    private function setPrices(array $prices, string $pricesType)
    {
        $this->pricesType = $pricesType;
        $priceProperty    = 'net' === $this->pricesType ? 'netPrices' : 'grossPrices';

        if (0 < count($prices)) {
            foreach ($prices as $currency => $price) {
                $currency = new Currency($currency);

                if (isset($price[SubscriptionInterface::MONTHLY])) {
                    $amount = $price[SubscriptionInterface::MONTHLY];
                    if ( ! $amount instanceof MoneyInterface) {
                        $amount = new Money([
                            'baseAmount' => $price[SubscriptionInterface::MONTHLY], 'currency' => $currency,
                        ]);
                    }
                    $this->$priceProperty[$currency->getCode()][SubscriptionInterface::MONTHLY] = $amount;
                }

                if (isset($price[SubscriptionInterface::YEARLY])) {
                    $amount = $price[SubscriptionInterface::YEARLY];
                    if ( ! $amount instanceof MoneyInterface) {
                        $amount = new Money([
                            'baseAmount' => $price[SubscriptionInterface::YEARLY], 'currency' => $currency,
                        ]);
                    }
                    $this->$priceProperty[$currency->getCode()][SubscriptionInterface::YEARLY] = $amount;
                }
            }
        }
    }

    /**
     * @param string $currency
     * @param string $subscriptionInterval
     * @param string $pricesType
     *
     * @return MoneyInterface
     */
    private function calculateInstantPrice(string $currency, string $subscriptionInterval, string $pricesType): MoneyInterface
    {
        if ( ! $this->subscription instanceof SubscriptionInterface) {
            throw new \RuntimeException('Before you can get instant prices you have to set a Subscription with setSubscription().');
        }
        $price = $this->getPrice($currency, $subscriptionInterval, $pricesType);

        // If the feature is not already subscribed or if it was subscribed today
        if (null === $this->subscription->getSubscribedOn() || ($this->subscription->getSubscribedOn()->format('Y-m-d') === (new \DateTime())->format('Y-m-d'))) {
            // ...the user has never paid, so he has no remaining days of subscription and has to pay the full price
            return $price;
        }

        switch ($subscriptionInterval) {
            case SubscriptionInterface::MONTHLY:
                // Our ideal month is ever of 31 days
                $daysInInterval = 31;
                break;
            case SubscriptionInterface::YEARLY:
                // Our ideal year is ever of 365 days
                $daysInInterval = 365;
                break;
            default:
                throw new \InvalidArgumentException(sprintf('The subscription interval can be only "%s" or "%s". "%s" passed.', SubscriptionInterface::MONTHLY, SubscriptionInterface::YEARLY, $subscriptionInterval));
        }

        $pricePerDay = (int) floor($price->getBaseAmount() / $daysInInterval);

        // Calculate the remaining days
        $nextRenewOn = clone $this->subscription->getNextRenewOn();

        /** @var \DateInterval $remainingDays */
        $remainingDays = $nextRenewOn->diff(new \DateTime());

        /* @var \DateInterval $remainingDays */
        $instantPrice = $pricePerDay * $remainingDays->days;

        return new Money(['baseAmount' => $instantPrice, 'currency' => $currency]);
    }
}
