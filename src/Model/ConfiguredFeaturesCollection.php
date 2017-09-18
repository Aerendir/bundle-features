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

    /**
     * {@inheritdoc}
     */
    public function __construct(array $elements = [])
    {
        FeaturesFactory::setKind(self::KIND);
        parent::__construct($elements);
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
    }
}
