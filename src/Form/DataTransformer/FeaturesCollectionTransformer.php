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

use SerendipityHQ\Bundle\FeaturesBundle\Model\SubscribedFeaturesCollection;
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
     * @return SubscribedFeaturesCollection
     */
    public function transform($features)
    {
        return new SubscribedFeaturesCollection($features);
    }

    /**
     * Transforms a form value into a Feature object.
     *
     * @param SubscribedFeaturesCollection $features
     *
     * @return SubscribedFeaturesCollection
     */
    public function reverseTransform($features)
    {
        return $features;
    }
}
