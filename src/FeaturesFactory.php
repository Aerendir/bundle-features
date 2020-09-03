<?php

/*
 * This file is part of the Serendipity HQ Features Bundle.
 *
 * Copyright (c) Adamo Aerendir Crespi <aerendir@serendipityhq.com>.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SerendipityHQ\Bundle\FeaturesBundle;

use SerendipityHQ\Bundle\FeaturesBundle\Model\ConfiguredBooleanFeature;
use SerendipityHQ\Bundle\FeaturesBundle\Model\ConfiguredCountableFeature;
use SerendipityHQ\Bundle\FeaturesBundle\Model\ConfiguredFeaturesCollection;
use SerendipityHQ\Bundle\FeaturesBundle\Model\ConfiguredRechargeableFeature;
use SerendipityHQ\Bundle\FeaturesBundle\Model\SubscribedBooleanFeature;
use SerendipityHQ\Bundle\FeaturesBundle\Model\SubscribedCountableFeature;
use SerendipityHQ\Bundle\FeaturesBundle\Model\SubscribedFeaturesCollection;
use SerendipityHQ\Bundle\FeaturesBundle\Model\SubscribedRechargeableFeature;

/**
 * Creates Features objects.
 */
final class FeaturesFactory
{
    /** @var string $kind */
    private static $kind;

    public static function getKind(): string
    {
        self::checkKindIsSet();

        return self::$kind;
    }

    public static function setKind(string $kind): void
    {
        if (false === \in_array($kind, [ConfiguredFeaturesCollection::KIND, SubscribedFeaturesCollection::KIND])) {
            throw new \InvalidArgumentException(\Safe\sprintf('Features kind can be only "configured" or "subscribed". You passed "%s".', $kind));
        }
        self::$kind = $kind;
    }

    /**
     * @return ConfiguredBooleanFeature|SubscribedBooleanFeature|null
     */
    public static function createBoolean(string $name, array $details = [])
    {
        self::checkKindIsSet();

        switch (self::$kind) {
            case ConfiguredFeaturesCollection::KIND:
                return new ConfiguredBooleanFeature($name, $details);
                break;
            case SubscribedFeaturesCollection::KIND:
                return new SubscribedBooleanFeature($name, $details);
                break;
        }

        return null;
    }

    /**
     * @return ConfiguredCountableFeature|SubscribedCountableFeature|null
     */
    public static function createCountable(string $name, array $details = [])
    {
        self::checkKindIsSet();

        switch (self::$kind) {
            case ConfiguredFeaturesCollection::KIND:
                return new ConfiguredCountableFeature($name, $details);
                break;
            case SubscribedFeaturesCollection::KIND:
                return new SubscribedCountableFeature($name, $details);
                break;
        }

        return null;
    }

    /**
     * @return ConfiguredRechargeableFeature|SubscribedRechargeableFeature|null
     */
    public static function createRechargeable(string $name, array $details = [])
    {
        self::checkKindIsSet();

        switch (self::$kind) {
            case ConfiguredFeaturesCollection::KIND:
                return new ConfiguredRechargeableFeature($name, $details);
                break;
            case SubscribedFeaturesCollection::KIND:
                return new SubscribedRechargeableFeature($name, $details);
                break;
        }

        return null;
    }

    public static function checkKindIsSet(): void
    {
        if (null === self::$kind) {
            throw new \LogicException('Before you can create features you have to set the kind you want to generate. Use FeaturesFactory::setKind().');
        }
    }
}
