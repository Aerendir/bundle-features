<?php

/*
 * This file is part of the Serendipity HQ Features Bundle.
 *
 * Copyright (c) Adamo Aerendir Crespi <aerendir@serendipityhq.com>.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SerendipityHQ\Bundle\FeaturesBundle\Model;

use SerendipityHQ\Bundle\FeaturesBundle\Property\IsRecurringFeatureProperty;

/**
 * {@inheritdoc}
 */
class SubscribedBooleanFeature extends AbstractSubscribedFeature implements SubscribedBooleanFeatureInterface
{
    use IsRecurringFeatureProperty {
        IsRecurringFeatureProperty::__construct as RecurringFeatureConstruct;
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

    /**
     * {@inheritdoc}
     */
    public function toArray()
    {
        return array_merge([
            'active_until' => json_decode(json_encode($this->getActiveUntil()), true),
            'enabled'      => $this->isEnabled(),
        ], parent::toArray());
    }
}
