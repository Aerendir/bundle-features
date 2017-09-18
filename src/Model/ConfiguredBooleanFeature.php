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

use SerendipityHQ\Bundle\FeaturesBundle\Property\CanBeFreeProperty;
use SerendipityHQ\Bundle\FeaturesBundle\Property\HasRecurringPricesProperty;

/**
 * {@inheritdoc}
 */
class ConfiguredBooleanFeature extends AbstractFeature implements ConfiguredBooleanFeatureInterface
{
    use HasRecurringPricesProperty {
        HasRecurringPricesProperty::__construct as RecurringConstruct;
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
    public function disable(): FeatureInterface
    {
        $this->enabled = false;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function enable(): FeatureInterface
    {
        $this->enabled = true;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function isEnabled(): bool
    {
        return $this->enabled;
    }
}
