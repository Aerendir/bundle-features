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
