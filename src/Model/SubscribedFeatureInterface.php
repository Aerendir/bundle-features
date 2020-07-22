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
