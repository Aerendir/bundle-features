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

use SerendipityHQ\Bundle\FeaturesBundle\Property\CanBeConsumedInterface;

/**
 * {@inheritdoc}
 */
interface SubscribedRechargeableFeatureInterface extends SubscribedFeatureInterface, CanBeConsumedInterface
{
    public function getLastRechargeOn(): \DateTime;

    public function getLastRechargeQuantity(): int;

    public function getRechargingPack(): SubscribedRechargeableFeaturePack;

    public function hasRechargingPack(): bool;

    public function recharge(): SubscribedRechargeableFeatureInterface;

    public function setRecharginPack(SubscribedRechargeableFeaturePack $rechargingPack): SubscribedRechargeableFeatureInterface;
}
