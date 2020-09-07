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

    private const ENABLED = 'enabled';

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
        if (isset($details[self::ENABLED]) && true === $details[self::ENABLED]) {
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
            'active_until'     => \Safe\json_decode(\Safe\json_encode($this->getActiveUntil()), true),
            self::ENABLED      => $this->isEnabled(),
        ], parent::toArray());
    }
}
