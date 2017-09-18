<?php

/*
 * This file is part of the SHQFeaturesBundle.
 *
 * Copyright Adamo Aerendir Crespi 2016-2017.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    Adamo Aerendir Crespi <hello@aerendir.me>
 * @copyright Copyright (C) 2016 - 2017 Aerendir. All rights reserved.
 * @license   MIT License.
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
