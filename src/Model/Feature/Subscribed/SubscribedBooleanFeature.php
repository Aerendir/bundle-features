<?php

/*
 * This file is part of the Serendipity HQ Features Bundle.
 *
 * Copyright (c) Adamo Aerendir Crespi <aerendir@serendipityhq.com>.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SerendipityHQ\Bundle\FeaturesBundle\Model\Feature\Subscribed;

use function Safe\json_decode;
use function Safe\json_encode;
use SerendipityHQ\Bundle\FeaturesBundle\Model\Feature\Property\CanBeEnabledInterface;
use SerendipityHQ\Bundle\FeaturesBundle\Model\Feature\Property\CanBeEnabledProperty;
use SerendipityHQ\Bundle\FeaturesBundle\Model\Feature\Property\IsRecurringFeatureInterface;
use SerendipityHQ\Bundle\FeaturesBundle\Model\Feature\Property\IsRecurringFeatureProperty;

final class SubscribedBooleanFeature extends AbstractSubscribedFeature implements CanBeEnabledInterface, IsRecurringFeatureInterface, SubscribedFeatureInterface
{
    use CanBeEnabledProperty;
    use IsRecurringFeatureProperty {
        IsRecurringFeatureProperty::__construct as RecurringFeatureConstruct;
    }

    /** @var bool $enabled */
    private $enabled = false;

    public function __construct(string $name, array $details = [])
    {
        // Set the type
        $details[self::FIELD_TYPE] = self::TYPE_BOOLEAN;

        $this->disable();
        if (isset($details[CanBeEnabledInterface::FIELD_ENABLED]) && true === $details[CanBeEnabledInterface::FIELD_ENABLED]) {
            $this->enable();
        }

        $this->RecurringFeatureConstruct($details);

        parent::__construct($name, $details);
    }

    /**
     * {@inheritdoc}
     */
    public function toArray(): array
    {
        return \array_merge([
            IsRecurringFeatureInterface::FIELD_ACTIVE_UNTIL => json_decode(json_encode($this->getActiveUntil(), JSON_THROW_ON_ERROR), true, 512, JSON_THROW_ON_ERROR),
            CanBeEnabledInterface::FIELD_ENABLED            => $this->isEnabled(),
        ], parent::toArray());
    }
}
