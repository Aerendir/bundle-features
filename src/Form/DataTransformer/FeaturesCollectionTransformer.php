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

use SerendipityHQ\Bundle\FeaturesBundle\Model\FeaturesCollection;
use Symfony\Component\Form\DataTransformerInterface;

/**
 * {@inheritdoc}
 */
class FeaturesCollectionTransformer implements DataTransformerInterface
{
    /**
     * Transforms a Feature object into the right value to be set in the form.
     *
     * @param array $features
     *
     * @return FeaturesCollection
     */
    public function transform($features)
    {
        return new FeaturesCollection($features);
    }

    /**
     * Transforms a form value into a Feature object.
     *
     * @param FeaturesCollection $features
     *
     * @return FeaturesCollection
     */
    public function reverseTransform($features)
    {
        return $features;
    }
}
