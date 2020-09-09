<?php

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
 * Implemented by features or packages that can be free.
 *
 * A Feature or a Pack is free if its price property is empty.
 */
interface CanBeFreeInterface
{
    public function isFree(): bool;
}
