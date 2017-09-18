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

namespace SerendipityHQ\Bundle\FeaturesBundle\Form\DataTransformer;

use SerendipityHQ\Bundle\FeaturesBundle\Model\SubscribedRechargeableFeature;
use SerendipityHQ\Bundle\FeaturesBundle\Model\SubscribedRechargeableFeatureInterface;
use SerendipityHQ\Bundle\FeaturesBundle\Model\SubscribedRechargeableFeaturePack;

/**
 * {@inheritdoc}
 */
class RechargeableFeatureTransformer extends AbstractFeatureTransformer
{
    /**
     * Transforms a Feature object into the right value to be set in the form.
     *
     * @param SubscribedRechargeableFeature|null $feature
     *
     * @return string
     */
    public function transform($feature)
    {
        // As we haven't a default option, we always return 0
        return 0;
    }

    /**
     * Transforms a form value into a Feature object.
     *
     * @param string $pack
     *
     * @return SubscribedRechargeableFeatureInterface
     */
    public function reverseTransform($pack)
    {
        // Also if it seems useless in this moment as we could use directly $pack, we use the configured pack as in the
        // future here will set also the price at which the pack were bought
        $configuredPack = $this->getConfiguredPack($pack);
        $subscribedPack = new SubscribedRechargeableFeaturePack(['num_of_units' => $configuredPack->getNumOfUnits()]);

        /** @var SubscribedRechargeableFeatureInterface $subscribedFeature */
        $subscribedFeature = $this->getCurrentTransformingFeature();
        $subscribedFeature->setRecharginPack($subscribedPack);

        // Call recharge, so the form can automatically update the feature
        $subscribedFeature->recharge();

        return $subscribedFeature;
    }
}
