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

use SerendipityHQ\Bundle\FeaturesBundle\Model\Feature\FeatureInterface;
use SerendipityHQ\Bundle\FeaturesBundle\Model\Feature\Subscribed\SubscribedBooleanFeature;

/**
 * {@inheritdoc}
 */
final class BooleanFeatureTransformer extends AbstractFeatureTransformer
{
    /**
     * Transforms a Feature object into the right value to be set in the form.
     *
     * @param FeatureInterface|null $feature
     */
    public function transform($feature): bool
    {
        if ($feature instanceof SubscribedBooleanFeature) {
            return $feature->isEnabled();
        }

        return false;
    }

    /**
     * Transforms a form value into a Feature object.
     *
     * @param bool $enabled
     *
     * @return FeatureInterface
     */
    public function reverseTransform($enabled)
    {
        /** @var SubscribedBooleanFeature $subscribedFeature */
        $subscribedFeature = $this->getCurrentTransformingFeature();

        switch ($enabled) {
            case true:
                $subscribedFeature->enable();
                break;
            case false:
                $subscribedFeature->disable();
        }

        return $subscribedFeature;
    }
}
