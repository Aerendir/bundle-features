<?php

namespace SerendipityHQ\Bundle\FeaturesBundle\Model;

/**
 * {@inheritdoc}
 */
class RechargeableFeature extends AbstractFeature implements RechargeableFeatureInterface
{
    /**
     * {@inheritdoc}
     */
    public function __construct(string $name, array $details = [])
    {
        // Set the type
        $details['type'] = self::RECHARGEABLE;

        die(dump($details));

        parent::__construct($name, $details);
    }
}
