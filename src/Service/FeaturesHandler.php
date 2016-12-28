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

use SerendipityHQ\Bundle\FeaturesBundle\Model\BooleanFeatureInterface;
use SerendipityHQ\Bundle\FeaturesBundle\Model\FeatureInterface;
use SerendipityHQ\Bundle\FeaturesBundle\Model\FeaturesCollection;
use SerendipityHQ\Bundle\FeaturesBundle\Model\RechargeableFeatureInterface;
use SerendipityHQ\Bundle\FeaturesBundle\Model\SubscriptionInterface;

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

    /** @var  SubscriptionInterface $subscription */
    private $subscription;

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
        if (null === $type)
            return $this->features;

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
     * @param SubscriptionInterface $subscription
     */
    public function setSubscription(SubscriptionInterface $subscription)
    {
        /** @var FeatureInterface $feature */
        foreach ($this->features as $feature) {
            if (null !== $subscription->getNextPaymentOn())
                $feature->setValidUntil($subscription->getNextPaymentOn());
        }
    }
}
