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

use SerendipityHQ\Bundle\FeaturesBundle\Model\FeatureInterface;
use SerendipityHQ\Bundle\FeaturesBundle\Model\SubscribedBooleanFeatureInterface;

/**
 * {@inheritdoc}
 */
class BooleanFeatureTransformer extends AbstractFeatureTransformer
{
    /**
     * Transforms a Feature object into the right value to be set in the form.
     *
     * @param FeatureInterface|null $feature
     *
     * @return string
     */
    public function transform($feature)
    {
        if ($feature instanceof SubscribedBooleanFeatureInterface) {
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
        /** @var SubscribedBooleanFeatureInterface $subscribedFeature */
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
