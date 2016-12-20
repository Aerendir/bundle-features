<?php

/*
 * This file is part of the Trust Back Me Www.
 *
 * Copyright Adamo Aerendir Crespi 2012-2016.
 *
 * This code is to consider private and non disclosable to anyone for whatever reason.
 * Every right on this code is reserved.
 *
 * @author    Adamo Aerendir Crespi <hello@aerendir.me>
 * @copyright Copyright (C) 2012 - 2016 Aerendir. All rights reserved.
 * @license   SECRETED. No distribution, no copy, no derivative, no divulgation or any other activity or action that
 *            could disclose this text.
 */

namespace SerendipityHQ\Bundle\FeaturesBundle\Util;

use SerendipityHQ\Component\ValueObjects\Currency\Currency;
use SerendipityHQ\Component\ValueObjects\Money\Money;

/**
 * Methods to handle premium plans.
 */
class PremiumPlansNavigator
{
    /** @var array */
    private static $plans = [];

    /**
     * Checks plans are set.
     */
    public static function checkPlansAreNotEmpty()
    {
        if (empty(self::$plans)) {
            throw  new \BadMethodCallException('No plan is set. Use PremiumPlansNavigator::setPlans() to set them.');
        }
    }

    /**
     * @param string $feature
     *
     * @return bool
     */
    public static function getDefaultStatusForBoolean($feature)
    {
        self::checkPlansAreNotEmpty();

        if (false === isset(self::$plans['boolean'][$feature]['enabled'])) {
            throw new \InvalidArgumentException(
                sprintf('The feature "%s" doesn\'t exist.', $feature)
            );
        }

        return self::$plans['boolean'][$feature]['enabled'];
    }

    /**
     * @param string   $feature
     * @param Currency $currency
     * @param string   $interval
     *
     * @return Money
     */
    public static function getPriceForBoolean($feature, Currency $currency, $interval)
    {
        self::checkPlansAreNotEmpty();

        // Check interval
        if (is_int($interval) && 1 === $interval) {
            $interval = 'month';
        } elseif (is_int($interval) && 12 === $interval) {
            $interval = 'year';
        } elseif ('month' !== $interval && 'year' !== $interval) {
            throw new \InvalidArgumentException(
                sprintf('The interval "%s" you requested doesn\'t exist. Allowed intervals are "month" and "year".', $interval)
            );
        }

        if (false === isset(self::$plans['boolean'][$feature]['price'][$currency->toString()][$interval])) {
            throw new \InvalidArgumentException(
                sprintf('The price of feature "%s" doesn\'t exist in the currency "%s" in the given "%s" interval.', $feature, $currency, $interval)
            );
        }

        $price = self::$plans['boolean'][$feature]['price'][$currency->toString()][$interval];

        // If the feature is enabled by default, its price is 0, it's free! :D
        $amount = self::$plans['boolean'][$feature]['enabled'] ? 0 : $price;

        return new Money(['amount' => $amount, 'currency' => $currency]);
    }

    /**
     * @param array $plans
     */
    public static function setPlans(array $plans)
    {
        self::$plans = $plans;
    }
}
