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
use SerendipityHQ\Bundle\FeaturesBundle\Property\HasRecurringPricesInterface;
use SerendipityHQ\Bundle\FeaturesBundle\Property\HasUnatantumPricesInterface;

/**
 * {@inheritdoc}
 */
class ConfiguredFeaturesCollection extends AbstractFeaturesCollection
{
    const KIND = 'configured';

    /** @var bool $taxSet */
    private $taxSet = false;

    /**
     * {@inheritdoc}
     */
    public function __construct(array $elements = [])
    {
        FeaturesFactory::setKind(self::KIND);
        parent::__construct($elements);
    }

    /**
     * @return bool
     */
    public function isTaxSet(): bool
    {
        return $this->taxSet;
    }

    /**
     * @param SubscriptionInterface $subscription
     *
     * @return $this
     */
    public function setSubscription(SubscriptionInterface $subscription)
    {
        foreach ($this->getValues() as $feature) {
            if ($feature instanceof HasRecurringPricesInterface || $feature instanceof ConfiguredCountableFeatureInterface) {
                $feature->setSubscription($subscription);
            }
        }

        return $this;
    }

    /**
     * @param float $rate
     */
    public function setTax(float $rate, string $name)
    {
        foreach ($this->getValues() as $feature) {
            if (
                $feature instanceof HasRecurringPricesInterface
                || $feature instanceof HasUnatantumPricesInterface
                // ConfiguredCountableFeatureInterface doesn't support Unitary Price and so doesn't implement HasRecurringPricesInterface
                || $feature instanceof ConfiguredCountableFeatureInterface
            ) {
                /** @var ConfiguredCountableFeatureInterface|HasRecurringPricesInterface|HasUnatantumPricesInterface $feature */
                $feature->setTax($rate, $name);
            }
        }

        $this->taxSet = true;
    }
}
