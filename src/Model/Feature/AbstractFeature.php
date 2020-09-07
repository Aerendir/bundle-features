<?php

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
