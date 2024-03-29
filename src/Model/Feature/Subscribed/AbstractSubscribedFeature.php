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

namespace SerendipityHQ\Bundle\FeaturesBundle\Model\Feature\Subscribed;

use SerendipityHQ\Bundle\FeaturesBundle\Model\Feature\AbstractFeature;
use SerendipityHQ\Bundle\FeaturesBundle\Model\Feature\Configured\ConfiguredFeatureInterface;

abstract class AbstractSubscribedFeature extends AbstractFeature implements SubscribedFeatureInterface
{
    private ConfiguredFeatureInterface $configuredFeature;

    public function getConfiguredFeature(): ConfiguredFeatureInterface
    {
        if (null === $this->configuredFeature) {
            throw new \LogicException('The configured feature of this subscribed feature is not set. Use FeaturesManager::setSubscription to set the correspondent configured feature in each subscribed feature of the subscription.');
        }

        return $this->configuredFeature;
    }

    public function setConfiguredFeature(ConfiguredFeatureInterface $configuredFeature): void
    {
        $this->configuredFeature = $configuredFeature;
    }

    public function toArray(): array
    {
        return [
            self::FIELD_TYPE => $this->getType(),
        ];
    }
}
