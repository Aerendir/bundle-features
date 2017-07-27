<?php

namespace SerendipityHQ\Bundle\FeaturesBundle\Model;

/**
 * Common interface for all type of feature.
 */
interface FeatureInterface
{
    const BOOLEAN = 'boolean';
    const COUNTABLE = 'countable';
    const RECHARGEABLE = 'rechargeable';

    /**
     * FeatureInterface constructor.
     *
     * @param string $name
     * @param array  $details
     */
    public function __construct(string $name, array $details = []);

    /**
     * @return string
     */
    public function getName() : string;

    /**
     * @return string
     */
    public function getType() : string;
}
