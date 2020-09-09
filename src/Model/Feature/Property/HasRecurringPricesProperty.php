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
use SerendipityHQ\Bundle\FeaturesBundle\Model\Feature\FeatureInterface;
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
    private $instantGrossPrices;

    /** @var array $instantNetPrices */
    private $instantNetPrices;

    /** @var array $grossPrices */
    private $grossPrices;

    /** @var array $netPrices */
    private $netPrices;

    /** @var string $pricesType */
    private $pricesType;

    /** @var SubscriptionInterface $subscription */
    private $subscription;

    /** @var string $taxName */
    private $taxName;

    /** @var float $taxRate */
    private $taxRate;

    public function __construct(array $details = [])
    {
        $this->instantGrossPrices = [];
        $this->instantNetPrices   = [];
        $this->grossPrices        = [];
        $this->netPrices          = [];
        if (isset($details[HasRecurringPricesInterface::FIELD_NET_PRICES])) {
            $this->setPrices($details[HasRecurringPricesInterface::FIELD_NET_PRICES], FeatureInterface::PRICE_NET);
        }

        if (isset($details[HasRecurringPricesInterface::FIELD_GROSS_PRICES])) {
            $this->setPrices($details[HasRecurringPricesInterface::FIELD_GROSS_PRICES], FeatureInterface::PRICE_GROSS);
        }
    }

    /**
     * @param Currency|string $currency This is not typecasted so the method can be called from inside Twig templates simply passing a string
     *
     * @throws \InvalidArgumentException If the $subscriptionInterval does not exist
     *
     * @return MoneyInterface|null if the price is not set in the required currency
     */
    public function getInstantPrice($currency, string $subscriptionInterval, string $type = null): ?MoneyInterface
    {
        if ($currency instanceof Currency) {
            $currency = $currency->getCode();
        }

        if (null === $type) {
            $type = $this->pricesType;
        }

        $instantPricesProperty = FeatureInterface::PRICE_NET === $type ? 'instantNetPrices' : 'instantGrossPrices';
        $instantPrices         = $this->$instantPricesProperty;

        if (null !== $instantPrices && false === \is_array($instantPrices)) {
            throw new \RuntimeException('Something went wrong returning instant prices.');
        }

        if (false === isset($instantPrices[$currency][$subscriptionInterval])) {
            $instantPrices[$currency][$subscriptionInterval] = $this->calculateInstantPrice($currency, $subscriptionInterval, $type);
            $this->$instantPricesProperty = $instantPrices;
        }

        return $instantPrices[$currency][$subscriptionInterval] ?? null;
    }

    /**
     * @param Currency|string $currency This is not typecasted so the method can be called from inside Twig templates simply passing a string
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

        return $this->getPrices($type)[$currency][$subscriptionInterval] ?? new Money([MoneyInterface::BASE_AMOUNT => 0, MoneyInterface::CURRENCY => new Currency($currency)]);
    }

    public function getPrices(string $type = null): ?array
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

    public function getTaxName(): string
    {
        return $this->taxName;
    }

    public function getTaxRate(): float
    {
        return $this->taxRate;
    }

    /**
     * @param Currency|string $currency This is not typecasted so the method can be called from inside Twig templates simply passing a string
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

    public function setSubscription(SubscriptionInterface $subscription): HasRecurringPricesInterface
    {
        $this->subscription = $subscription;

        /** @var HasRecurringPricesInterface $this */
        return $this;
    }

    public function setTax(float $rate, string $name): HasRecurringPricesInterface
    {
        $this->taxName = $name;
        $this->taxRate = $rate;

        $pricesProperty = FeatureInterface::PRICE_NET === $this->pricesType ? 'netPrices' : 'grossPrices';
        // ... Then we have to set gross prices
        if (\is_countable($this->$pricesProperty) && 0 < \count($this->$pricesProperty)) {
            foreach ($this->$pricesProperty as $currency => $prices) {
                /** @var MoneyInterface $price */
                foreach ($prices as $subscriptionInterval => $price) {
                    switch ($this->pricesType) {
                        // If currently is "net"...
                        case FeatureInterface::PRICE_NET:
                            $netPrice                                            = (int) \round($price->getBaseAmount() * (1 + $rate));
                            $netPrice                                            = new Money([MoneyInterface::BASE_AMOUNT => $netPrice, MoneyInterface::CURRENCY => $currency]);
                            $this->grossPrices[$currency][$subscriptionInterval] = $netPrice;
                            break;
                        // If currently is "gross"...
                        case FeatureInterface::PRICE_GROSS:
                            // ... Then we have to set net prices
                            $grossPrice                                        = (int) \round($price->getBaseAmount() / (1 + $rate));
                            $grossPrice                                        = new Money([MoneyInterface::BASE_AMOUNT => $grossPrice, MoneyInterface::CURRENCY => $currency]);
                            $this->netPrices[$currency][$subscriptionInterval] = $grossPrice;
                            break;
                    }
                }
            }
        }

        /** @var HasRecurringPricesInterface $this */
        return $this;
    }

    private function setPrices(array $settingPrices, string $pricesType)
    {
        $this->pricesType = $pricesType;
        $priceProperty    = FeatureInterface::PRICE_NET === $this->pricesType ? 'netPrices' : 'grossPrices';
        $setPrices        = $this->$priceProperty;

        if (0 < \count($settingPrices)) {
            foreach ($settingPrices as $currency => $price) {
                $currency = new Currency($currency);

                if (isset($price[SubscriptionInterface::MONTHLY])) {
                    $amount = $price[SubscriptionInterface::MONTHLY];
                    if ( ! $amount instanceof MoneyInterface) {
                        $amount = new Money([
                            MoneyInterface::BASE_AMOUNT => $price[SubscriptionInterface::MONTHLY], MoneyInterface::CURRENCY => $currency,
                        ]);
                    }
                    $setPrices[$currency->getCode()][SubscriptionInterface::MONTHLY] = $amount;
                }

                if (isset($price[SubscriptionInterface::YEARLY])) {
                    $amount = $price[SubscriptionInterface::YEARLY];
                    if ( ! $amount instanceof MoneyInterface) {
                        $amount = new Money([
                            MoneyInterface::BASE_AMOUNT => $price[SubscriptionInterface::YEARLY], MoneyInterface::CURRENCY => $currency,
                        ]);
                    }
                    $setPrices[$currency->getCode()][SubscriptionInterface::YEARLY] = $amount;
                }
            }
        }

        $this->$priceProperty = $setPrices;
    }

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
                throw new \InvalidArgumentException(\Safe\sprintf('The subscription interval can be only "%s" or "%s". "%s" passed.', SubscriptionInterface::MONTHLY, SubscriptionInterface::YEARLY, $subscriptionInterval));
        }

        $pricePerDay = (int) \floor($price->getBaseAmount() / $daysInInterval);

        // Calculate the remaining days
        $nextRenewOn = clone $this->subscription->getNextRenewOn();

        /** @var \DateInterval $remainingDays */
        $remainingDays = $nextRenewOn->diff(new \DateTime());

        $instantPrice = $pricePerDay * $remainingDays->days;

        return new Money([MoneyInterface::BASE_AMOUNT => $instantPrice, MoneyInterface::CURRENCY => $currency]);
    }
}
