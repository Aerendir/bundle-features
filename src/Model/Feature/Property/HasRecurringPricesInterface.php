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
use SerendipityHQ\Bundle\FeaturesBundle\Model\SubscriptionInterface;
use SerendipityHQ\Component\ValueObjects\Money\MoneyInterface;

/**
 * Common methods to manage a feature that is bought in a recurring subscription.
 */
interface HasRecurringPricesInterface extends CanBeFreeInterface
{
    public const FIELD_NET_PRICES   = 'net_prices';

    public const FIELD_GROSS_PRICES = 'gross_prices';

    /**
     * @param Currency|string $currency This is not typecasted so the method can be called from inside Twig templates simply passing a string
     *
     * @throws \InvalidArgumentException If the $subscriptionInterval does not exist
     *
     * @return MoneyInterface|null if the price is not set in the required currency
     */
    public function getInstantPrice($currency, string $subscriptionInterval, string $type = null): ?MoneyInterface;

    /**
     * @param Currency|string $currency This is not typecasted so the method can be called from inside Twig templates simply passing a string
     */
    public function getPrice($currency, string $subscriptionInterval, string $type = null): MoneyInterface;

    public function getPrices(string $type = null): ?array;

    public function getTaxName(): string;

    public function getTaxRate(): float;

    /**
     * @param Currency|string $currency This is not typecasted so the method can be called from inside Twig templates simply passing a string
     */
    public function hasPrice($currency, string $subscriptionInterval, string $type = null): bool;

    public function setSubscription(SubscriptionInterface $subscription): HasRecurringPricesInterface;

    public function setTax(float $rate, string $name): HasRecurringPricesInterface;
}
