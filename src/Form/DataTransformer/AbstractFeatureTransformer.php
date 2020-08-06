<?php

/*
 * This file is part of the Serendipity HQ Features Bundle.
 *
 * Copyright (c) Adamo Aerendir Crespi <aerendir@serendipityhq.com>.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
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
    /** @var array|null Used only by Countable and Rechargeable features */
    private $configuredPacks;

    /** @var string $field */
    private $featureName;

    /** @var SubscribedFeaturesCollection|null $subscribedFeatures */
    private $subscribedFeatures;

    /**
     * @param string                       $featureName
     * @param SubscribedFeaturesCollection $subscribedFeatures
     * @param array|null                   $configuredPacks
     */
    public function __construct(string $featureName, SubscribedFeaturesCollection $subscribedFeatures, array $configuredPacks = null)
    {
        $this->configuredPacks    = $configuredPacks;
        $this->featureName        = $featureName;
        $this->subscribedFeatures = $subscribedFeatures;
    }

    /**
     * @param int $pack
     *
     * @return ConfiguredCountableFeaturePack|ConfiguredRechargeableFeaturePack
     */
    public function getConfiguredPack(int $pack)
    {
        if (null === $this->configuredPacks) {
            throw new \LogicException('To get configured packs you have to first pass them when instantiating the DataTransformer.');
        }

        if (false === isset($this->configuredPacks[$pack])) {
            throw new \RuntimeException(\Safe\sprintf('The requested pack "%s" doesn\'t exist', $pack));
        }

        return $this->configuredPacks[$pack];
    }

    /**
     * @return string
     */
    public function getFeatureName(): string
    {
        return $this->featureName;
    }

    /**
     * @return SubscribedFeatureInterface
     */
    public function getCurrentTransformingFeature(): SubscribedFeatureInterface
    {
        return $this->getSubscribedFeatures()->get($this->getFeatureName());
    }

    public function getSubscribedFeatures(): SubscribedFeaturesCollection
    {
        return $this->subscribedFeatures;
    }
}
