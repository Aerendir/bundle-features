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

use SerendipityHQ\Bundle\FeaturesBundle\Property\HasRecurringPricesInterface;

/**
 * {@inheritdoc}
 */
interface ConfiguredBooleanFeatureInterface extends HasRecurringPricesInterface, ConfiguredFeatureInterface
{
    /**
     * @return FeatureInterface
     */
    public function disable(): FeatureInterface;

    /**
     * @return FeatureInterface
     */
    public function enable(): FeatureInterface;

    /**
     * @return bool
     */
    public function isEnabled(): bool;
}
