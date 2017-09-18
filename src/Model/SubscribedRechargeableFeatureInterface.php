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

use SerendipityHQ\Bundle\FeaturesBundle\Property\CanBeConsumedInterface;

/**
 * {@inheritdoc}
 */
interface SubscribedRechargeableFeatureInterface extends SubscribedFeatureInterface, CanBeConsumedInterface
{
    /**
     * @return \DateTime
     */
    public function getLastRechargeOn(): \DateTime;

    /**
     * @return int
     */
    public function getLastRechargeQuantity(): int;

    /**
     * @return SubscribedRechargeableFeaturePack
     */
    public function getRechargingPack(): SubscribedRechargeableFeaturePack;

    /**
     * @return bool
     */
    public function hasRechargingPack(): bool;

    /**
     * @return SubscribedRechargeableFeatureInterface
     */
    public function recharge(): SubscribedRechargeableFeatureInterface;

    /**
     * @param SubscribedRechargeableFeaturePack $rechargingPack
     *
     * @return SubscribedRechargeableFeatureInterface
     */
    public function setRecharginPack(SubscribedRechargeableFeaturePack $rechargingPack): SubscribedRechargeableFeatureInterface;
}
