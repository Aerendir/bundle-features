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

abstract class AbstractFeaturePack implements FeaturePackInterface
{
    /** @var int $numOfUnits How many units are contained in this Pack */
    private int $numOfUnits;

    public function __construct(array $details)
    {
        $this->numOfUnits = $details[FeaturePackInterface::FIELD_NUM_OF_UNITS];
    }

    public function getNumOfUnits(): int
    {
        return $this->numOfUnits;
    }
}
