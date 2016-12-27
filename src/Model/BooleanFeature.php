<?php

namespace SerendipityHQ\Bundle\FeaturesBundle\Model;

/**
 * {@inheritdoc}
 */
class BooleanFeature extends AbstractFeature implements BooleanFeatureInterface
{
    /**
     * {@inheritdoc}
     */
    public function __construct(string $name, array $details = [])
    {
        // Set the type
        $details['type'] = self::BOOLEAN;

        parent::__construct($name, $details);
    }
}
