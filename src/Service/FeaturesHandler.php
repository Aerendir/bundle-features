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

use Doctrine\Common\Collections\ArrayCollection;
use SerendipityHQ\Bundle\FeaturesBundle\Model\BooleanFeatureInterface;
use SerendipityHQ\Bundle\FeaturesBundle\Model\FeatureInterface;
use SerendipityHQ\Bundle\FeaturesBundle\Model\FeaturesCollection;
use SerendipityHQ\Bundle\FeaturesBundle\Model\FeaturesManagerInterface;
use SerendipityHQ\Bundle\FeaturesBundle\Model\RechargeableFeatureInterface;
use SerendipityHQ\Component\ValueObjects\Currency\Currency;
use SerendipityHQ\Component\ValueObjects\Money\Money;

/**
 * Class to navigate the features tree.
 */
final class FeaturesHandler
{
    /** @var FeaturesCollection $features */
    private $features;

    /** @var FeaturesCollection $boolean */
    private $booleans;

    /** @var FeaturesCollection $rechargeables */
    private $rechargeables;

    /**
     * @param array $features
     */
    public function __construct(array $features)
    {
        $this->features = new FeaturesCollection($features);
    }

    /**
     * Returns the full array of features.
     *
     * It returns a specific kind of features set if specified.
     *
     * @param string $type
     *
     * @return FeaturesCollection
     */
    public function getFeatures(string $type = null) : FeaturesCollection
    {
        if (null !== $type) {
            switch ($type) {
                case FeatureInterface::BOOLEAN:
                    if (null === $this->booleans) {
                        $predictate = function ($element) {
                            if ($element instanceof BooleanFeatureInterface)
                                return $element;
                        };

                        // Cache the result
                        $this->booleans = $this->features->filter($predictate);
                    }

                    return $this->booleans;
                    break;
                case FeatureInterface::RECHARGEABLE:
                    if (null === $this->rechargeables) {
                        $predictate = function ($element) {
                            if ($element instanceof RechargeableFeatureInterface)
                                return $element;
                        };

                        // Cache the result
                        $this->rechargeables = $this->features->filter($predictate);
                    }

                    return $this->rechargeables;
                    break;
                default:
                    throw new \InvalidArgumentException(sprintf('The feature type "%s" does not exist.', $type));
            }
        }

        return $this->features;
    }

    /**
     * @param string $feature
     *
     * @return bool
     */
    public function getDefaultStatusForBoolean(string $feature) : bool
    {
        return $this->getBooleanFeature($feature)->isEnabled();
    }

    /**
     * @param string $feature
     * @return BooleanFeatureInterface
     */
    public function getBooleanFeature(string $feature) : BooleanFeatureInterface
    {
        $return = $this->getFeatures(FeatureInterface::BOOLEAN)->get($feature);
        if (null === $return) {
            throw new \InvalidArgumentException(
                sprintf('The feature "%s" doesn\'t exist.', $feature)
            );
        }

        return $return;
    }

    /**
     * @param string   $feature
     * @param Currency $currency
     * @param string   $interval
     *
     * @return Money
     *
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
     * */
}
