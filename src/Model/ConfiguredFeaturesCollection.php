<?php

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
     * @return $this
     */
    public function setSubscription(SubscriptionInterface $subscription)
    {
        foreach ($this->getValues() as $feature) {
            if ($feature instanceof HasRecurringPricesInterface || $feature instanceof ConfiguredCountableFeatureInterface)
                $feature->setSubscription($subscription);
        }

        return $this;
    }

    /**
     * @param float $rate
     */
    public function setTaxRate(float $rate)
    {
        foreach ($this->getValues() as $feature) {
            if (
                $feature instanceof HasRecurringPricesInterface
                || $feature instanceof HasUnatantumPricesInterface
                // ConfiguredCountableFeatureInterface doesn't support Unitary Price and so doesn't implement HasRecurringPricesInterface
                || $feature instanceof ConfiguredCountableFeatureInterface
            )
                /** @var HasRecurringPricesInterface|HasUnatantumPricesInterface|ConfiguredCountableFeatureInterface $feature */
                $feature->setTaxRate($rate);
        }
    }
}
