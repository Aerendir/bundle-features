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

/**
 * A Feature loaded from a subscription.
 */
interface SubscribedFeatureInterface extends FeatureInterface
{
    /**
     * @return ConfiguredBooleanFeatureInterface|ConfiguredCountableFeatureInterface|ConfiguredFeatureInterface|ConfiguredRechargeableFeatureInterface
     */
    public function getConfiguredFeature(): ConfiguredFeatureInterface;

    /**
     * @param ConfiguredFeatureInterface $configuredFeature
     */
    public function setConfiguredFeature(ConfiguredFeatureInterface $configuredFeature);

    /**
     * Converts a Feature object into an array.
     *
     * @return array
     */
    public function toArray();
}
