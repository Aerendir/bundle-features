<?php

namespace SerendipityHQ\Bundle\FeaturesBundle\Model;

use SerendipityHQ\Bundle\FeaturesBundle\Property\CanBeFreeProperty;
use SerendipityHQ\Bundle\FeaturesBundle\Property\RecurringPricesProperty;

/**
 * {@inheritdoc}
 */
class ConfiguredBooleanFeature extends AbstractFeature implements ConfiguredBooleanFeatureInterface
{
    use RecurringPricesProperty {
        RecurringPricesProperty::__construct as RecurringConstruct;
    }
    use CanBeFreeProperty;

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

        $this->RecurringConstruct($details);

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
}
