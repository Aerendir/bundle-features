<?php

namespace SerendipityHQ\Bundle\FeaturesBundle\Model;

use SerendipityHQ\Bundle\FeaturesBundle\FeaturesFactory;
use SerendipityHQ\Bundle\FeaturesBundle\Property\HasRecurringPricesInterface;

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
            if ($feature instanceof HasRecurringPricesInterface)
                $feature->setSubscription($subscription);
        }

        return $this;
    }
}
