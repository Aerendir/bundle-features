<?php

namespace SerendipityHQ\Bundle\FeaturesBundle\Model;

interface FeatureInterface
{
    const BOOLEAN = 'boolean';
    const RECHARGEABLE = 'rechargeable';
    public function __construct(string $name, array $details = []);
    public function disable() : FeatureInterface;

    /**
     * @return FeatureInterface
     */
    public function enable() : FeatureInterface;

    /**
     * @return string
     */
    public function getName() : string;

    /**
     * @return string
     */
    public function getType() : string;

    /**
     * @return bool
     */
    public function isEnabled() : bool;
}
