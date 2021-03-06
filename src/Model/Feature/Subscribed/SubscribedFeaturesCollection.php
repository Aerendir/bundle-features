<?php

/*
 * This file is part of the Serendipity HQ Features Bundle.
 *
 * Copyright (c) Adamo Aerendir Crespi <aerendir@serendipityhq.com>.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SerendipityHQ\Bundle\FeaturesBundle\Model\Feature\Subscribed;

use SerendipityHQ\Bundle\FeaturesBundle\Model\Feature\AbstractFeaturesCollection;
use SerendipityHQ\Bundle\FeaturesBundle\Model\Feature\FeatureInterface;

final class SubscribedFeaturesCollection extends AbstractFeaturesCollection implements \JsonSerializable
{
    public const KIND = 'subscribed';

    /**
     * {@inheritdoc}
     */
    public function __construct(array $elements = [])
    {
        parent::__construct(self::KIND, $elements);
    }

    /**
     * {@inheritdoc}
     */
    public function toArray(): array
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
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    /**
     * @return SubscribedFeaturesCollection&SubscribedBooleanFeature[]
     */
    protected function getBooleanFeatures(): \Countable
    {
        if (null === $this->booleans) {
            // Cache the result
            $this->booleans = $this->filter($this->getFilterPredictate(self::KIND, FeatureInterface::TYPE_BOOLEAN));
        }

        return $this->booleans;
    }

    /**
     * @return SubscribedFeaturesCollection&SubscribedCountableFeature[]
     */
    protected function getCountableFeatures(): \Countable
    {
        if (null === $this->countables) {
            // Cache the result
            $this->countables = $this->filter($this->getFilterPredictate(self::KIND, FeatureInterface::TYPE_COUNTABLE));
        }

        return $this->countables;
    }

    /**
     * @return SubscribedFeaturesCollection&SubscribedRechargeableFeature[]
     */
    protected function getRechargeableFeatures(): \Countable
    {
        if (null === $this->rechargeables) {
            // Cache the result
            $this->rechargeables = $this->filter($this->getFilterPredictate(self::KIND, FeatureInterface::TYPE_RECHARGEABLE));
        }

        return $this->rechargeables;
    }
}
