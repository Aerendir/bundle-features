<?php

namespace SerendipityHQ\Bundle\FeaturesBundle\Model;

use SerendipityHQ\Bundle\FeaturesBundle\Property\RecurringFeatureProperty;

/**
 * {@inheritdoc}
 */
class SubscribedBooleanFeature extends AbstractFeature implements SubscribedBooleanFeatureInterface
{
    use RecurringFeatureProperty {
        RecurringFeatureProperty::__construct as RecurringFeatureConstruct;
    }

    /** @var bool $enabled */
    private $enabled = false;

    /**
     * {@inheritdoc}
     */
    public function __construct(string $name, array $details = [])
    {
        // Set the type
        $details['type'] = self::BOOLEAN;

        $this->disable();
        if (isset($details['enabled']) && true === $details['enabled']) {
            $this->enable();
        }

        $this->RecurringFeatureConstruct($details);

        parent::__construct($name, $details);
    }

    /**
     * {@inheritdoc}
     */
    public function disable() : FeatureInterface
    {
        $this->enabled = false;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function enable() : FeatureInterface
    {
        $this->enabled = true;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function isEnabled() : bool
    {
        return $this->enabled;
    }

    /**
     * {@inheritdoc}
     */
    public function toArray()
    {
        return array_merge([
            'active_until' => json_decode(json_encode($this->getActiveUntil()), true),
            'enabled' => $this->isEnabled(),
        ], parent::toArray());
    }
}
