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

use SerendipityHQ\Bundle\FeaturesBundle\Model\Feature\Property\IsRecurringFeatureInterface;
use SerendipityHQ\Bundle\FeaturesBundle\Model\Feature\Property\IsRecurringFeatureProperty;

/**
 * {@inheritdoc}
 */
final class SubscribedBooleanFeature extends AbstractSubscribedFeature implements IsRecurringFeatureInterface, SubscribedFeatureInterface
{
    use IsRecurringFeatureProperty {
        IsRecurringFeatureProperty::__construct as RecurringFeatureConstruct;
    }

    public const FIELD_ACTIVE_UNTIL = 'active_until';
    public const FIELD_ENABLED      = 'enabled';

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

        $this->RecurringFeatureConstruct($details);

        parent::__construct($name, $details);
    }

    /**
     * {@inheritdoc}
     */
    public function disable(): self
    {
        $this->enabled = false;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function enable(): self
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

    /**
     * {@inheritdoc}
     */
    public function toArray(): array
    {
        return \array_merge([
            self::FIELD_ACTIVE_UNTIL      => \Safe\json_decode(\Safe\json_encode($this->getActiveUntil()), true),
            self::FIELD_ENABLED           => $this->isEnabled(),
        ], parent::toArray());
    }
}
