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

namespace SerendipityHQ\Bundle\FeaturesBundle\Service;

use SerendipityHQ\Component\ValueObjects\Currency\Currency;
use SerendipityHQ\Component\ValueObjects\Money\Money;

/**
 * Class to navigate the features tree.
 */
final class FeaturesHandler
{
    const BOOLEAN = 'boolean';

    /** @var array */
    private $features = [];

    /**
     * @param array $features
     */
    public function __construct(array $features)
    {
        $this->features = $features;
    }

    /**
     * @param array $features
     * @return FeaturesHandler
     */
    public static function create(array $features) : self
    {
        return new static($features);
    }

    /**
     * Returns the full array of features.
     *
     * It returns a specific kind of features set if specified.
     *
     * @param string $kind
     *
     * @return array
     */
    public function getFeatures(string $kind = null) : array
    {
        if (null !== $kind && in_array($kind, [self::BOOLEAN])) {
            // If features of this kind doesn't exist...
            if (false === isset($this->features[$kind])) {
                // ... Return an empty array
                return [];
            }

            // Return the array with the features of the specified kind
            return $this->features[$kind];
        }

        return $this->features;
    }

    /**
     * @param string $feature
     * @return array
     */
    public function getBooleanFeature(string $feature) : array
    {
        if (false === isset($this->getFeatures(self::BOOLEAN)[$feature])) {
            throw new \InvalidArgumentException(
                sprintf('The feature "%s" doesn\'t exist.', $feature)
            );
        }

        return $this->getFeatures(self::BOOLEAN)[$feature];
    }

    /**
     * @param string $feature
     *
     * @return bool
     */
    public function getDefaultStatusForBoolean(string $feature)
    {
        return $this->getBooleanFeature($feature)['enabled'];
    }

    /**
     * @param string   $feature
     * @param Currency $currency
     * @param string   $interval
     *
     * @return Money
     */
    public function getPriceForBoolean($feature, Currency $currency, $interval)
    {
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

        if (false === isset($this->features['boolean'][$feature]['price'][$currency->toString()][$interval])) {
            throw new \InvalidArgumentException(
                sprintf('The price of feature "%s" doesn\'t exist in the currency "%s" in the given "%s" interval.', $feature, $currency, $interval)
            );
        }

        $price = $this->features['boolean'][$feature]['price'][$currency->toString()][$interval];

        // If the feature is enabled by default, its price is 0, it's free! :D
        $amount = $this->features['boolean'][$feature]['enabled'] ? 0 : $price;

        return new Money(['amount' => $amount, 'currency' => $currency]);
    }
}
