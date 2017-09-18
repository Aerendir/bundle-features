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
 * {@inheritdoc}
 */
abstract class AbstractFeaturePack implements FeaturePackInterface
{
    /** @var int $numOfUnits How many units are contained in this Pack */
    private $numOfUnits;

    /**
     * @param array $details
     */
    public function __construct(array $details)
    {
        $this->numOfUnits = $details['num_of_units'];
    }

    /**
     * {@inheritdoc}
     */
    public function getNumOfUnits(): int
    {
        return $this->numOfUnits;
    }
}
