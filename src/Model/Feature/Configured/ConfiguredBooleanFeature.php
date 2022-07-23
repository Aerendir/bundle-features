<?php

declare(strict_types=1);

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
use SerendipityHQ\Bundle\FeaturesBundle\Model\Feature\Property\CanBeEnabledInterface;
use SerendipityHQ\Bundle\FeaturesBundle\Model\Feature\Property\CanBeEnabledProperty;
use SerendipityHQ\Bundle\FeaturesBundle\Model\Feature\Property\CanBeFreeProperty;
use SerendipityHQ\Bundle\FeaturesBundle\Model\Feature\Property\HasRecurringPricesInterface;
use SerendipityHQ\Bundle\FeaturesBundle\Model\Feature\Property\HasRecurringPricesProperty;

final class ConfiguredBooleanFeature extends AbstractFeature implements CanBeEnabledInterface, HasRecurringPricesInterface, ConfiguredFeatureInterface
{
    use CanBeEnabledProperty;
    use CanBeFreeProperty;
    use HasRecurringPricesProperty {
        HasRecurringPricesProperty::__construct as RecurringPricesConstruct;
    }

    public function __construct(string $name, array $details = [])
    {
        // Set the type
        $details[self::FIELD_TYPE] = self::TYPE_BOOLEAN;

        $this->disable();
        if (isset($details[CanBeEnabledInterface::FIELD_ENABLED]) && true === $details[CanBeEnabledInterface::FIELD_ENABLED]) {
            $this->enable();
        }

        $this->RecurringPricesConstruct($details);

        parent::__construct($name, $details);
    }
}
