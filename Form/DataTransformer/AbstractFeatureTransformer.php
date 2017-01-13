<?php

/*
 * This file is part of the Trust Back Me Www.
 *
 * Copyright Adamo Aerendir Crespi 2012-2016.
 *
 * This code is to consider private and non disclosable to anyone for whatever reason.
 * Every right on this code is reserved.
 *
 * @author    Adamo Aerendir Crespi <hello@aerendir.me>
 * @copyright Copyright (C) 2012 - 2016 Aerendir. All rights reserved.
 * @license   SECRETED. No distribution, no copy, no derivative, no divulgation or any other activity or action that
 *            could disclose this text.
 */

namespace SerendipityHQ\Bundle\FeaturesBundle\Form\DataTransformer;

use SerendipityHQ\Bundle\FeaturesBundle\Model\ConfiguredCountableFeaturePack;
use SerendipityHQ\Bundle\FeaturesBundle\Model\ConfiguredRechargeableFeaturePack;
use SerendipityHQ\Bundle\FeaturesBundle\Model\SubscribedFeatureInterface;
use SerendipityHQ\Bundle\FeaturesBundle\Model\SubscribedFeaturesCollection;
use Symfony\Component\Form\DataTransformerInterface;

/**
 * {@inheritdoc}
 */
abstract class AbstractFeatureTransformer implements DataTransformerInterface
{
    /** @var  array|null Used only by Countable and Rechargeable features */
    private $configuredPacks;

    /** @var string $field */
    private $featureName;

    /** @var null|SubscribedFeaturesCollection $subscribedFeatures */
    private $subscribedFeatures;

    /**
     * @param string $featureName
     * @param SubscribedFeaturesCollection $subscribedFeatures
     * @param array|null $configuredPacks
     */
    public function __construct(string $featureName, SubscribedFeaturesCollection $subscribedFeatures, array $configuredPacks = null)
    {
        $this->configuredPacks = $configuredPacks;
        $this->featureName = $featureName;
        $this->subscribedFeatures = $subscribedFeatures;
    }

    /**
     * @param int $pack
     * @return ConfiguredCountableFeaturePack|ConfiguredRechargeableFeaturePack
     */
    public function getConfiguredPack(int $pack)
    {
        if (null === $this->configuredPacks) {
            throw new \LogicException('To get configured packs you have to first pass them when instantiating the DataTransformer.');
        }

        if (false === isset($this->configuredPacks[$pack])) {
            throw new \RuntimeException(sprintf('The requested pack "%s" doesn\'t exist', $pack));
        }

        return $this->configuredPacks[$pack];
    }

    /**
     * @return string
     */
    public function getFeatureName() : string
    {
        return $this->featureName;
    }

    /**
     * @return SubscribedFeatureInterface
     */
    public function getCurrentTransformingFeature() : SubscribedFeatureInterface
    {
        return $this->getSubscribedFeatures()->get($this->getFeatureName());
    }

    /**
     * @return SubscribedFeaturesCollection
     */
    public function getSubscribedFeatures() : SubscribedFeaturesCollection
    {
        return $this->subscribedFeatures;
    }
}
