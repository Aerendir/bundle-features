<?php

namespace SerendipityHQ\Bundle\FeaturesBundle\Property;

use SerendipityHQ\Bundle\FeaturesBundle\Model\ConfiguredRechargeableFeatureInterfaceSimple;
use SerendipityHQ\Bundle\FeaturesBundle\Model\ConfiguredRechargeableFeaturePack;
use SerendipityHQ\Bundle\FeaturesBundle\Model\FeatureInterface;
use SerendipityHQ\Bundle\FeaturesBundle\Model\SubscriptionInterface;
use SerendipityHQ\Component\ValueObjects\Currency\CurrencyInterface;
use SerendipityHQ\Component\ValueObjects\Money\MoneyInterface;

/**
 * Common methods to manage a feature that is bought in a recurring subscription.
 */
interface HasRecurringPricesInterface
{
    /**
     * @param string|CurrencyInterface $currency             This is not typecasted so the method can be called from inside Twig templates simply passing a string
     * @param string          $subscriptionInterval
     *
     * @throws \InvalidArgumentException If the $subscriptionInterval does not exist
     *
     * @return MoneyInterface|null if the price is not set in the required currency
     */
    public function getInstantPrice($currency, string $subscriptionInterval) : MoneyInterface;

    /**
     * @param $currency
     * @param string $subscriptionInterval
     * @return MoneyInterface
     */
    public function getPrice($currency, string $subscriptionInterval) : MoneyInterface;

    /**
     * @return array
     */
    public function getPrices() : array;

    /**
     * @param $currency
     * @param string $subscriptionInterval
     * @return bool
     */
    public function hasPrice($currency, string $subscriptionInterval) : bool;

    /**
     * @param SubscriptionInterface $subscription
     * @return HasRecurringPricesInterface
     */
    public function setSubscription(SubscriptionInterface $subscription) : HasRecurringPricesInterface;
}
