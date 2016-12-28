<?php

namespace SerendipityHQ\Bundle\FeaturesBundle\Model;


use SerendipityHQ\Component\ValueObjects\Currency\Currency;
use SerendipityHQ\Component\ValueObjects\Currency\CurrencyInterface;
use SerendipityHQ\Component\ValueObjects\Money\MoneyInterface;

/**
 * Interface for a Subscription.
 */
interface SubscriptionInterface
{
    const MONTHLY = 'monthly';
    const YEARLY  = 'yearly';

    /**
     * @param string $featureName
     * @param FeatureInterface $feature
     * @return SubscriptionInterface
     */
    public function addFeature(string $featureName, FeatureInterface $feature) : SubscriptionInterface;

    /**
     * Get id.
     *
     * @return int
     */
    public function getId() : int;

    /**
     * @return Currency
     */
    public function getCurrency();

    /**
     * @return FeaturesCollection
     */
    public function getFeatures();

    /**
     * @return string
     */
    public function getInterval() : string ;

    /**
     * @return MoneyInterface
     */
    public function getNextPaymentAmount() : MoneyInterface;

    /**
     * If the date of the next payment is not set, use the creation date.
     * If it is not set, is because this is a new subscription, so the next payment is immediate.
     *
     * The logic of the app will set this date one month or one year in the future.
     *
     * @return \DateTime
     */
    public function getNextPaymentOn();

    /**
     * @param string $feature
     * @return bool
     */
    public function has(string $feature) : bool;

    /**
     * @param int $id
     *
     * @return SubscriptionInterface
     */
    public function setId(int $id) : SubscriptionInterface;

    /**
     * @param CurrencyInterface $currency
     * @return SubscriptionInterface
     */
    public function setCurrency(CurrencyInterface $currency) : SubscriptionInterface;

    /**
     * @param array $features
     */
    public function setFeatures(array $features);

    /**
     * @param string $interval
     *
     * @return SubscriptionInterface
     */
    public function setInterval(string $interval) : SubscriptionInterface;

    /**
     * @return SubscriptionInterface
     */
    public function setMonthly() : SubscriptionInterface;

    /**
     * @return SubscriptionInterface
     */
    public function setYearly() : SubscriptionInterface;

    /**
     * @param MoneyInterface $amount
     *
     * @return SubscriptionInterface
     */
    public function setNextPaymentAmount(MoneyInterface $amount) : SubscriptionInterface;

    /**
     * @param \DateTime $nextPaymentOn
     *
     * @return SubscriptionInterface
     */
    public function setNextPaymentOn(\DateTime $nextPaymentOn) : SubscriptionInterface;

    /**
     * Sets the next payment in one month.
     *
     * @return SubscriptionInterface
     */
    public function setNextPaymentInOneMonth() : SubscriptionInterface;

    /**
     * Sets the next payment in one month.
     *
     * @return SubscriptionInterface
     */
    public function setNextPaymentInTwelveMonths() : SubscriptionInterface;

    /**
     * @return string
     */
    public function __toString() : string;
}
