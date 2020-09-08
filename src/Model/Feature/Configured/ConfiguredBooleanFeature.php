<?php

/*
 * This file is part of the Serendipity HQ Features Bundle.
 *
 * Copyright (c) Adamo Aerendir Crespi <aerendir@serendipityhq.com>.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SerendipityHQ\Bundle\FeaturesBundle\Model\Feature\Configured;

use SerendipityHQ\Bundle\FeaturesBundle\Model\Feature\AbstractFeature;
use SerendipityHQ\Bundle\FeaturesBundle\Model\Feature\Property\CanBeFreeProperty;
use SerendipityHQ\Bundle\FeaturesBundle\Model\Feature\Property\HasRecurringPricesInterface;
use SerendipityHQ\Bundle\FeaturesBundle\Model\Feature\Property\HasRecurringPricesProperty;

final class ConfiguredBooleanFeature extends AbstractFeature implements HasRecurringPricesInterface, ConfiguredFeatureInterface
{
    use HasRecurringPricesProperty {
        HasRecurringPricesProperty::__construct as RecurringConstruct;
    }
    use CanBeFreeProperty;

    private const FIELD_ENABLED = 'enabled';

    /** @var bool $enabled */
    private $enabled = false;

    /**
     * {@inheritdoc}
     */
    public function __construct(string $name, array $details = [])
    {
        // Set the type
        $details[self::FIELD_TYPE] = self::TYPE_BOOLEAN;

        $this->disable();
        if (isset($details[self::FIELD_ENABLED]) && true === $details[self::FIELD_ENABLED]) {
            $this->enable();
        }

        $this->RecurringConstruct($details);

        parent::__construct($name, $details);
    }

    public function disable(): ConfiguredBooleanFeature
    {
        $this->enabled = false;

        return $this;
    }

    public function enable(): ConfiguredBooleanFeature
    {
        $this->enabled = true;

        return $this;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }
}
