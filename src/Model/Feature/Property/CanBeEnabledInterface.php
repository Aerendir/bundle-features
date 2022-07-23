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

namespace SerendipityHQ\Bundle\FeaturesBundle\Model\Feature\Property;

/**
 * Implemented by features or packages that can be enabled.
 *
 * In this moment, only Boolean features use this property.
 */
interface CanBeEnabledInterface
{
    public const FIELD_ENABLED = 'enabled';

    public function disable(): CanBeEnabledInterface;

    public function enable(): CanBeEnabledInterface;

    public function isEnabled(): bool;
}
