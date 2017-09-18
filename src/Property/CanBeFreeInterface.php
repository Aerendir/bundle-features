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

namespace SerendipityHQ\Bundle\FeaturesBundle\Property;

/**
 * Implemented by features or packages that can be free.
 *
 * A Feature or a Pack is free if its price property is empty.
 */
interface CanBeFreeInterface
{
    /**
     * @return bool
     */
    public function isFree(): bool;
}
