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

use SerendipityHQ\Bundle\FeaturesBundle\Model\Property\CanHaveFreePackInterface;
use SerendipityHQ\Bundle\FeaturesBundle\Model\Property\HasConfiguredPacksInterface;

/**
 * {@inheritdoc}
 */
interface ConfiguredCountableFeatureInterface extends HasConfiguredPacksInterface, CanHaveFreePackInterface, ConfiguredFeatureInterface
{
    public function getRefreshPeriod(): string;

    public function setSubscription(SubscriptionInterface $subscription): ConfiguredCountableFeatureInterface;

    public function setTax(float $rate, string $name): ConfiguredCountableFeatureInterface;
}
