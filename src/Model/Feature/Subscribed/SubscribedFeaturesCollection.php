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

namespace SerendipityHQ\Bundle\FeaturesBundle\Model\Feature\Subscribed;

use Doctrine\Common\Collections\ArrayCollection;
use SerendipityHQ\Bundle\FeaturesBundle\Model\Feature\AbstractFeaturesCollection;
use SerendipityHQ\Bundle\FeaturesBundle\Model\Feature\FeatureInterface;

final class SubscribedFeaturesCollection extends AbstractFeaturesCollection implements \JsonSerializable
{
    public const KIND = 'subscribed';

    public function __construct(?array $elements = [])
    {
        parent::__construct(self::KIND, $elements);
    }

    public function toArray(): array
    {
        $return = [];
        foreach (parent::toArray() as $featureName => $featureDetils) {
            $return[$featureName] = $featureDetils->toArray();
        }

        return $return;
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    /**
     * @return SubscribedBooleanFeature[]|SubscribedFeaturesCollection
     */
    protected function getBooleanFeatures(): ArrayCollection
    {
        if (null === $this->booleans) {
            // Cache the result
            $this->booleans = $this->filter($this->getFilterPredictate(self::KIND, FeatureInterface::TYPE_BOOLEAN));
        }

        return $this->booleans;
    }

    /**
     * @return SubscribedCountableFeature[]|SubscribedFeaturesCollection
     */
    protected function getCountableFeatures(): ArrayCollection
    {
        if (null === $this->countables) {
            // Cache the result
            $this->countables = $this->filter($this->getFilterPredictate(self::KIND, FeatureInterface::TYPE_COUNTABLE));
        }

        return $this->countables;
    }

    /**
     * @return SubscribedFeaturesCollection|SubscribedRechargeableFeature[]
     */
    protected function getRechargeableFeatures(): ArrayCollection
    {
        if (null === $this->rechargeables) {
            // Cache the result
            $this->rechargeables = $this->filter($this->getFilterPredictate(self::KIND, FeatureInterface::TYPE_RECHARGEABLE));
        }

        return $this->rechargeables;
    }
}
