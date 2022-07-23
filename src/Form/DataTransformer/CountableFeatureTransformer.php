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
use SerendipityHQ\Bundle\FeaturesBundle\Model\Feature\Subscribed\SubscribedCountableFeature;
use SerendipityHQ\Bundle\FeaturesBundle\Model\Feature\Subscribed\SubscribedCountableFeaturePack;

final class CountableFeatureTransformer extends AbstractFeatureTransformer
{
    /**
     * Transforms a Feature object into the right value to be set in the form.
     *
     * @param SubscribedCountableFeature|null $feature
     */
    public function transform($feature): int
    {
        if ($feature instanceof SubscribedCountableFeature) {
            return $feature->getSubscribedPack()->getNumOfUnits();
        }

        return 0;
    }

    /**
     * Transforms a form value into a Feature object.
     *
     * @param int $pack
     */
    public function reverseTransform($pack): SubscribedCountableFeature
    {
        // Also if it seems useless in this moment as we could use directly $pack, we use the configured pack as in the
        // future here will set also the price at which the pack were bought
        $configuredPack = $this->getConfiguredPack($pack);
        $subscribedPack = new SubscribedCountableFeaturePack([FeaturePackInterface::FIELD_NUM_OF_UNITS => $configuredPack->getNumOfUnits()]);

        /** @var SubscribedCountableFeature $subscribedFeature */
        $subscribedFeature = $this->getCurrentTransformingFeature();
        $subscribedFeature->setSubscribedPack($subscribedPack);

        return $subscribedFeature;
    }
}
