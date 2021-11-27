<?php

/*
 * This file is part of the Serendipity HQ Features Bundle.
 *
 * Copyright (c) Adamo Aerendir Crespi <aerendir@serendipityhq.com>.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SerendipityHQ\Bundle\FeaturesBundle\Model\Feature\Configured;

use SerendipityHQ\Bundle\FeaturesBundle\Model\Feature\AbstractFeaturesCollection;
use SerendipityHQ\Bundle\FeaturesBundle\Model\Feature\FeatureInterface;
use SerendipityHQ\Bundle\FeaturesBundle\Model\Feature\Property\HasRecurringPricesInterface;
use SerendipityHQ\Bundle\FeaturesBundle\Model\Feature\Property\HasUnatantumPricesInterface;
use SerendipityHQ\Bundle\FeaturesBundle\Model\SubscriptionInterface;

final class ConfiguredFeaturesCollection extends AbstractFeaturesCollection
{
    public const KIND = 'configured';

    /** @var bool $taxSet */
    private $taxSet = false;

    /**
     * {@inheritdoc}
     */
    public function __construct(array $elements = [])
    {
        parent::__construct(self::KIND, $elements);
    }

    public function isTaxSet(): bool
    {
        return $this->taxSet;
    }

    public function setSubscription(SubscriptionInterface $subscription): self
    {
        foreach ($this->getValues() as $feature) {
            if ($feature instanceof HasRecurringPricesInterface || $feature instanceof ConfiguredCountableFeature) {
                $feature->setSubscription($subscription);
            }
        }

        return $this;
    }

    public function setTax(float $rate, string $name): void
    {
        foreach ($this->getValues() as $feature) {
            if (
                $feature instanceof HasRecurringPricesInterface
                || $feature instanceof HasUnatantumPricesInterface
                // ConfiguredCountableFeature doesn't support Unitary Price and so doesn't implement HasRecurringPricesInterface
                || $feature instanceof ConfiguredCountableFeature
            ) {
                /** @var ConfiguredCountableFeature|HasRecurringPricesInterface|HasUnatantumPricesInterface $feature */
                $feature->setTax($rate, $name);
            }
        }

        $this->taxSet = true;
    }

    /**
     * @return ConfiguredFeaturesCollection&ConfiguredBooleanFeature[]
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
     * @return ConfiguredFeaturesCollection&ConfiguredCountableFeature[]
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
     * @return ConfiguredFeaturesCollection&ConfiguredRechargeableFeature[]
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
