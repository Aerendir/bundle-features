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
 * Common interface for all type of feature.
 */
interface FeatureInterface
{
    /**
     * @var string
     */
    const BOOLEAN      = 'boolean';
    /**
     * @var string
     */
    const COUNTABLE    = 'countable';
    /**
     * @var string
     */
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
