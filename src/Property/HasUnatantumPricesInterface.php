<?php

/*
 * This file is part of the Serendipity HQ Features Bundle.
 *
 * Copyright (c) Adamo Aerendir Crespi <aerendir@serendipityhq.com>.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SerendipityHQ\Bundle\FeaturesBundle\Property;

use SerendipityHQ\Component\ValueObjects\Money\MoneyInterface;

/**
 * Common methods to manage feature prices.
 */
interface HasUnatantumPricesInterface
{
    /**
     * @param Currency|string $currency This is not typecasted so the method can be called from inside Twig templates simply passing a string
     * @param string|null     $type
     *
     * @return MoneyInterface|null if the price is not set in the required currency
     */
    public function getPrice($currency, string $type = null);

    /**
     * @param string|null $type
     *
     * @return array
     */
    public function getPrices(string $type = null): array;

    /**
     * @return string
     */
    public function getTaxName(): string;

    /**
     * @return float
     */
    public function getTaxRate(): float;

    /**
     * @param Currency|string $currency This is not typecasted so the method can be called from inside Twig templates simply passing a string
     * @param string|null     $type
     *
     * @return bool
     */
    public function hasPrice($currency, string $type = null): bool;

    /**
     * @param float  $rate
     * @param string $name
     *
     * @return HasUnatantumPricesInterface
     */
    public function setTax(float $rate, string $name): HasUnatantumPricesInterface;
}
