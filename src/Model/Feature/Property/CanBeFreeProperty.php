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

use SerendipityHQ\Component\ValueObjects\Money\Money;

/**
 * Concrete implementation of the CanBeFreeInterface.
 */
trait CanBeFreeProperty
{
    public function isFree(): bool
    {
        if (empty($this->netPrices) && empty($this->grossPrices)) {
            return true;
        }

        $prices = null;
        if (null !== $this->netPrices) {
            $prices = $this->netPrices;
        }

        if (null !== $this->grossPrices) {
            $prices = $this->grossPrices;
        }

        if (null !== $prices) {
            foreach ($prices as $currency => $billingCycles) {
                if (isset($billingCycles['monthly'])) {
                    $amount = $billingCycles['monthly'];
                    if ($amount instanceof Money && '0' === $amount->getBaseAmount()) {
                        return true;
                    }
                }

                if (isset($billingCycles['yearly'])) {
                    $amount = $billingCycles['yearly'];

                    if ($amount instanceof Money && '0' === $amount->getBaseAmount()) {
                        return true;
                    }
                }
            }
        }

        return false;
    }
}
