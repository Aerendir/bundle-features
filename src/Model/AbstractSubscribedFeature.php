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

/**
 * {@inheritdoc}
 */
abstract class AbstractSubscribedFeature extends AbstractFeature implements SubscribedFeatureInterface
{
    /** @var ConfiguredFeatureInterface $configuredFeature */
    private $configuredFeature;

    /**
     * @return ConfiguredFeatureInterface
     */
    public function getConfiguredFeature(): ConfiguredFeatureInterface
    {
        if (null === $this->configuredFeature) {
            throw new \LogicException('The configured feature of this subscribed feature is not set. Use FeaturesManager::setSubscription to set the correspondent configured feature in each subscribed feature of the subscription.');
        }

        return $this->configuredFeature;
    }

    /**
     * @param ConfiguredFeatureInterface $configuredFeature
     */
    public function setConfiguredFeature(ConfiguredFeatureInterface $configuredFeature)
    {
        $this->configuredFeature = $configuredFeature;
    }

    /**
     * {@inheritdoc}
     */
    public function toArray()
    {
        return [
            'type' => $this->getType(),
        ];
    }
}
