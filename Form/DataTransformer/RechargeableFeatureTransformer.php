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

use SerendipityHQ\Bundle\FeaturesBundle\Model\FeatureInterface;
use SerendipityHQ\Bundle\FeaturesBundle\Model\ConfiguredRechargeableFeature;

/**
 * {@inheritdoc}
 */
class RechargeableFeatureTransformer extends AbstractFeatureTransformer
{
    /**
     * Transforms a Feature object into the right value to be set in the form.
     *
     * @param ConfiguredRechargeableFeature|null $feature
     *
     * @return string
     */
    public function transform($feature)
    {
        if ($feature instanceof ConfiguredRechargeableFeature) {
            return $feature->getFreeRecharge();
        }

        if (null === $feature) {
            return 0;
        }

        return $feature;
    }

    /**
     * Transforms a form value into a Feature object.
     *
     * @param string $enabled
     *
     * @return FeatureInterface
     */
    public function reverseTransform($enabled)
    {
        die(dump($enabled));
    }
}
