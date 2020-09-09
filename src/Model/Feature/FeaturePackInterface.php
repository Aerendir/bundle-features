<?php

/*
 * This file is part of the Serendipity HQ Features Bundle.
 *
 * Copyright (c) Adamo Aerendir Crespi <aerendir@serendipityhq.com>.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SerendipityHQ\Bundle\FeaturesBundle\Model\Feature;

/**
 * Common interface for all type of feature packages.
 */
interface FeaturePackInterface
{
    public const FIELD_NUM_OF_UNITS = 'num_of_units';

    public function getNumOfUnits(): int;
}
