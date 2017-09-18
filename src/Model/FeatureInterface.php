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
 * Common interface for all type of feature.
 */
interface FeatureInterface
{
    const BOOLEAN      = 'boolean';
    const COUNTABLE    = 'countable';
    const RECHARGEABLE = 'rechargeable';

    /**
     * FeatureInterface constructor.
     *
     * @param string $name
     * @param array  $details
     */
    public function __construct(string $name, array $details = []);

    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @return string
     */
    public function getType(): string;
}
