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

use SerendipityHQ\Bundle\FeaturesBundle\Model\Feature\Configured\ConfiguredBooleanFeature;
use SerendipityHQ\Bundle\FeaturesBundle\Model\Feature\Configured\ConfiguredCountableFeature;
use SerendipityHQ\Bundle\FeaturesBundle\Model\Feature\Configured\ConfiguredFeaturesCollection;
use SerendipityHQ\Bundle\FeaturesBundle\Model\Feature\Configured\ConfiguredRechargeableFeature;
use SerendipityHQ\Bundle\FeaturesBundle\Model\Feature\Subscribed\SubscribedBooleanFeature;
use SerendipityHQ\Bundle\FeaturesBundle\Model\Feature\Subscribed\SubscribedCountableFeature;
use SerendipityHQ\Bundle\FeaturesBundle\Model\Feature\Subscribed\SubscribedFeaturesCollection;
use SerendipityHQ\Bundle\FeaturesBundle\Model\Feature\Subscribed\SubscribedRechargeableFeature;

/**
 * Creates Features objects.
 */
final class FeaturesFactory
{
    /**
     * @return ConfiguredBooleanFeature|SubscribedBooleanFeature|null
     */
    public static function createBoolean(string $kind, string $name, array $details = [])
    {
        switch ($kind) {
            case ConfiguredFeaturesCollection::KIND:
                return new ConfiguredBooleanFeature($name, $details);
            case SubscribedFeaturesCollection::KIND:
                return new SubscribedBooleanFeature($name, $details);
        }

        return null;
    }

    /**
     * @return ConfiguredCountableFeature|SubscribedCountableFeature|null
     */
    public static function createCountable(string $kind, string $name, array $details = [])
    {
        switch ($kind) {
            case ConfiguredFeaturesCollection::KIND:
                return new ConfiguredCountableFeature($name, $details);
            case SubscribedFeaturesCollection::KIND:
                return new SubscribedCountableFeature($name, $details);
        }

        return null;
    }

    /**
     * @return ConfiguredRechargeableFeature|SubscribedRechargeableFeature|null
     */
    public static function createRechargeable(string $kind, string $name, array $details = [])
    {
        switch ($kind) {
            case ConfiguredFeaturesCollection::KIND:
                return new ConfiguredRechargeableFeature($name, $details);
            case SubscribedFeaturesCollection::KIND:
                return new SubscribedRechargeableFeature($name, $details);
        }

        return null;
    }
}
