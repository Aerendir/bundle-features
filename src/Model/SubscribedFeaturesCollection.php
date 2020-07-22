<?php

/*
 * This file is part of the Serendipity HQ Features Bundle.
 *
 * Copyright (c) Adamo Aerendir Crespi <aerendir@serendipityhq.com>.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SerendipityHQ\Bundle\FeaturesBundle\Model;

use SerendipityHQ\Bundle\FeaturesBundle\FeaturesFactory;

/**
 * {@inheritdoc}
 */
class SubscribedFeaturesCollection extends AbstractFeaturesCollection implements \JsonSerializable
{
    const KIND = 'subscribed';

    /**
     * {@inheritdoc}
     */
    public function __construct(array $elements = [])
    {
        FeaturesFactory::setKind(self::KIND);
        parent::__construct($elements);
    }

    /**
     * {@inheritdoc}
     */
    public function toArray()
    {
        $return = [];
        /**
         * @var string
         * @var SubscribedFeatureInterface $featureDetils
         */
        foreach (parent::toArray() as $featureName => $featureDetils) {
            $return[$featureName] = $featureDetils->toArray();
        }

        return $return;
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }
}
