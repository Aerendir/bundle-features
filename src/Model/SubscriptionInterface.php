<?php

namespace SerendipityHQ\Bundle\FeaturesBundle\Model;

use SerendipityHQ\Component\ValueObjects\Currency\CurrencyInterface;
use SerendipityHQ\Component\ValueObjects\Money\MoneyInterface;

/**
 * Interface for a Subscription.
 */
interface SubscriptionInterface
{
    const DAILY = 'daily';
    const WEEKLY = 'weekly';
    const BIWEEKLY = 'biweekly';
    const MONTHLY = 'monthly';
    const YEARLY = 'yearly';

    /**
     * @param string $interval
     *
     * @return \DateTime
     */
    public static function calculateActiveUntil(string $interval) : \DateTime;

    /**
     * @param string $interval
     *
     * @throws \InvalidArgumentException If the $interval does not exist
     */
    public static function checkIntervalExists(string $interval);

    /**
     * @param string $interval
     *
     * @return bool
     */
    public static function intervalExists(string $interval) : bool;

    /**
     * @param string           $featureName
     * @param FeatureInterface $feature
     *
     * @return SubscriptionInterface
     */
    public function addFeature(string $featureName, FeatureInterface $feature) : SubscriptionInterface;

    /**
     * Do not set the return typecasting until a currency type is created.
     *
     * @return CurrencyInterface
     */
    public function getCurrency();

    /**
     * Forces the features to be returned as a ConfiguredFeaturesCollection.
     *
     * @return SubscribedFeaturesCollection
     */
    public function getFeatures() : SubscribedFeaturesCollection;

    /**
     * Get the current subscription interval.
     *
     * By default it is set to "monthly".
     *
     * @return string
     */
    public function getInterval() : string;

    /**
     * @return MoneyInterface
     */
    public function getNextPaymentAmount() : MoneyInterface;

    /**
     * @return null|string
     */
    public function getSmallestRenewInterval() :? string;

    /**
     * @return \DateTime|null
     */
    public function getNextRenewOn() :? \DateTime;

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
     * The date on which the feature were subscribed on.
     *
     * @return \DateTime
     */
    public function getSubscribedOn() : \DateTime;

    /**
     * @param string $feature
     *
     * @return bool
     */
    public function has(string $feature) : bool;

    /**
     * Shortcut method to check if a Feature in the subscription is still active.
     *
     * @param string $feature
     *
     * @return bool
     */
    public function isStillActive(string $feature) : bool;

    /**
     * @param CurrencyInterface $currency
     *
     * @return SubscriptionInterface
     */
    public function setCurrency(CurrencyInterface $currency) : SubscriptionInterface;

    /**
     * @param SubscribedFeaturesCollection $features
     *
     * @return SubscriptionInterface
     */
    public function setFeatures(SubscribedFeaturesCollection $features) : SubscriptionInterface;

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
    public function setNextPaymentInOneYear() : SubscriptionInterface;

    /**
     * @param string $renewInterval
     * @return SubscriptionInterface
     */
    public function setSmallestRenewInterval(string $renewInterval) : SubscriptionInterface;

    /**
     * @param \DateTime $nextRenewOn
     * @return SubscriptionInterface
     */
    public function setNextRenewOn(\DateTime $nextRenewOn) : SubscriptionInterface;

    /**
     * Sets the date on which the feature were subscribed.
     *
     * @param \DateTime $subscribedOn
     *
     * @return SubscriptionInterface
     */
    public function setSubscribedOn(\DateTime $subscribedOn) : SubscriptionInterface;
}