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
    const BOOLEAN      = 'boolean';

    const COUNTABLE    = 'countable';

    const RECHARGEABLE = 'rechargeable';

    /**
     * FeatureInterface constructor.
     */
    public function __construct(string $name, array $details = []);

    public function getName(): string;

    public function getType(): string;
}
