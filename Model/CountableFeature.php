<?php

namespace SerendipityHQ\Bundle\FeaturesBundle\Model;

use SerendipityHQ\Bundle\FeaturesBundle\Property\RecurringFeatureProperty;

/**
 * {@inheritdoc}
 */
class CountableFeature extends AbstractFeature implements CountableFeatureInterface
{
    use RecurringFeatureProperty {
        RecurringFeatureProperty::__construct as RecurringConstruct;
    }

    private $freeAmount;

    /**
     * {@inheritdoc}
     */
    public function __construct(string $name, array $details = [])
    {
        $this->freeAmount = $details['free_amount'] ?? 0;

        // Set the type
        $details['type'] = self::COUNTABLE;

        $this->RecurringConstruct($details);

        parent::__construct($name, $details);
    }

    /**
     * {@inheritdoc}
     */
    public function getFreeAmount() : int
    {
        return $this->freeAmount;
    }

    /**
     * {@inheritdoc}
     */
    public function toArray()
    {
        return array_merge([
            'active_until' => json_decode(json_encode($this->getActiveUntil()), true)
        ], parent::toArray());
    }
}
