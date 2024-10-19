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

namespace SerendipityHQ\Bundle\FeaturesBundle\Form\DataTransformer;

use SerendipityHQ\Bundle\FeaturesBundle\Model\Feature\FeaturePackInterface;
use SerendipityHQ\Bundle\FeaturesBundle\Model\Feature\Subscribed\SubscribedRechargeableFeature;
use SerendipityHQ\Bundle\FeaturesBundle\Model\Feature\Subscribed\SubscribedRechargeableFeaturePack;

final class RechargeableFeatureTransformer extends AbstractFeatureTransformer
{
    /**
     * Transforms a Feature object into the right value to be set in the form.
     *
     * @param SubscribedRechargeableFeature|null $feature
     */
    public function transform($feature): int
    {
        // As we haven't a default option, we always return 0
        return 0;
    }

    /**
     * Transforms a form value into a Feature object.
     *
     * @param string $pack
     */
    public function reverseTransform($pack): SubscribedRechargeableFeature
    {
        // Also if it seems useless in this moment as we could use directly $pack, we use the configured pack as in the
        // future here will set also the price at which the pack were bought
        $configuredPack = $this->getConfiguredPack($pack);
        $subscribedPack = new SubscribedRechargeableFeaturePack([FeaturePackInterface::FIELD_NUM_OF_UNITS => $configuredPack->getNumOfUnits()]);

        /** @var SubscribedRechargeableFeature $subscribedFeature */
        $subscribedFeature = $this->getCurrentTransformingFeature();
        $subscribedFeature->setRechargingPack($subscribedPack);

        // Call recharge, so the form can automatically update the feature
        $subscribedFeature->recharge();

        return $subscribedFeature;
    }
}
