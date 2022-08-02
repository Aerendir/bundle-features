<?php

declare(strict_types=1);

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
 * Common interface for all type of feature.
 */
interface FeatureInterface
{
    public const TYPE_BOOLEAN      = 'boolean';
    public const TYPE_COUNTABLE    = 'countable';
    public const TYPE_RECHARGEABLE = 'rechargeable';
    public const PRICE_GROSS       = 'gross';
    public const PRICE_NET         = 'net';
    public const FIELD_TYPE        = 'type';

    public function __construct(string $name, array $details = []);

    public function getName(): string;

    public function getType(): string;
}
