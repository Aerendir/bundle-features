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
abstract class AbstractFeature implements FeatureInterface
{
    /** @var string $name */
    private $name;

    /** @var string $type */
    private $type;

    /**
     * {@inheritdoc}
     */
    public function __construct(string $name, array $details = [])
    {
        $this->name = $name;
        $this->type = $details['type'];
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function getType(): string
    {
        return $this->type;
    }
}
