<?php

/*
 * This file is part of the Serendipity HQ Features Bundle.
 *
 * Copyright (c) Adamo Aerendir Crespi <aerendir@serendipityhq.com>.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SerendipityHQ\Bundle\FeaturesBundle\Property;

/**
 * Concrete implementetion of the CanBeFreeInterface.
 */
trait CanBeFreeProperty
{
    public function isFree(): bool
    {
        return empty($this->netPrices) && empty($this->grossPrices);
    }
}
