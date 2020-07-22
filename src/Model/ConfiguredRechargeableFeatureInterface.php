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

use SerendipityHQ\Bundle\FeaturesBundle\Property\HasConfiguredPacksInterface;
use SerendipityHQ\Bundle\FeaturesBundle\Property\HasUnatantumPricesInterface;

/**
 * {@inheritdoc}
 */
interface ConfiguredRechargeableFeatureInterface extends ConfiguredFeatureInterface, HasUnatantumPricesInterface, HasConfiguredPacksInterface
{
    /**
     * @return int
     */
    public function getFreeRecharge(): int;

    /**
     * @param int $freeRecharge
     *
     * @return ConfiguredRechargeableFeatureInterface
     */
    public function setFreeRecharge(int $freeRecharge): ConfiguredRechargeableFeatureInterface;
}
