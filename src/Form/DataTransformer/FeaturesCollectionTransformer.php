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
