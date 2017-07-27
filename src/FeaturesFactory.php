<?php

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
    /** @var  string $kind */
    private static $kind;

    /**
     * @return string
     */
    public static function getKind() : string
    {
        self::checkKindIsSet();

        return self::$kind;
    }

    /**
     * @param string $kind
     */
    public static function setKind(string $kind)
    {
        if (false === in_array($kind, [ConfiguredFeaturesCollection::KIND, SubscribedFeaturesCollection::KIND]))
            throw new \InvalidArgumentException(sprintf('Features kind can be only "configured" or "subscribed". You passed "%s".', $kind));

        self::$kind = $kind;
    }

    /**
     * @param string $name
     * @param array $details
     * @return null|ConfiguredBooleanFeature|SubscribedBooleanFeature
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
     * @param string $name
     * @param array $details
     * @return null|ConfiguredCountableFeature|SubscribedCountableFeature
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
     * @param string $name
     * @param array $details
     * @return null|ConfiguredRechargeableFeature|SubscribedRechargeableFeature
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

    public static function checkKindIsSet()
    {
        if (null === self::$kind)
            throw new \LogicException('Before you can create features you have to set the kind you want to generate. Use FeaturesFactory::setKind().');
    }
}
