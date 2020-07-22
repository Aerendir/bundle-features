<?php

/*
 * This file is part of the Serendipity HQ Features Bundle.
 *
 * Copyright (c) Adamo Aerendir Crespi <aerendir@serendipityhq.com>.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SerendipityHQ\Bundle\FeaturesBundle\Model;

use SerendipityHQ\Bundle\FeaturesBundle\Property\CanHaveFreePackInterface;
use SerendipityHQ\Bundle\FeaturesBundle\Property\HasConfiguredPacksInterface;

/**
 * {@inheritdoc}
 */
interface ConfiguredCountableFeatureInterface extends HasConfiguredPacksInterface, CanHaveFreePackInterface, ConfiguredFeatureInterface
{
    /**
     * @return string
     */
    public function getRefreshPeriod(): string;

    /**
     * @param SubscriptionInterface $subscription
     *
     * @return ConfiguredCountableFeatureInterface
     */
    public function setSubscription(SubscriptionInterface $subscription): ConfiguredCountableFeatureInterface;

    /**
     * @param float  $rate
     * @param string $name
     *
     * @return ConfiguredCountableFeatureInterface
     */
    public function setTax(float $rate, string $name): ConfiguredCountableFeatureInterface;
}
