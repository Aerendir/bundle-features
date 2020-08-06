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

use Money\Currency;
use SerendipityHQ\Component\ValueObjects\Money\MoneyInterface;

/**
 * Interface for a Subscription.
 */
interface SubscriptionInterface
{
    /**
     * @var string
     */
    const DAILY    = 'daily';
    /**
     * @var string
     */
    const WEEKLY   = 'weekly';
    /**
     * @var string
     */
    const BIWEEKLY = 'biweekly';
    /**
     * @var string
     */
    const MONTHLY  = 'monthly';
    /**
     * @var string
     */
    const YEARLY   = 'yearly';

    /**
     * @param string $interval
     *
     * @return \DateTime
     */
    public static function calculateActiveUntil(string $interval): \DateTime;

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
    public static function intervalExists(string $interval): bool;

    /**
     * @param string           $featureName
     * @param FeatureInterface $feature
     *
     * @return SubscriptionInterface
     */
    public function addFeature(string $featureName, FeatureInterface $feature): SubscriptionInterface;

    /**
     * Do not set the return typecasting until a currency type is created.
     */
    public function getCurrency(): \Money\Currency;

    /**
     * Forces the features to be returned as a ConfiguredFeaturesCollection.
     *
     * @return SubscribedFeaturesCollection
     */
    public function getFeatures(): SubscribedFeaturesCollection;

    /**
     * Get the current subscription interval.
     *
     * By default it is set to "monthly".
     *
     * @return string
     */
    public function getRenewInterval(): string;

    /**
     * @return MoneyInterface
     */
    public function getNextRenewAmount(): MoneyInterface;

    /**
     * @return string|null
     */
    public function getSmallestRefreshInterval(): ? string;

    /**
     * @return \DateTime|null
     */
    public function getNextRefreshOn(): ? \DateTime;

    /**
     * If the date of the next payment is not set, use the creation date.
     * If it is not set, is because this is a new subscription, so the next payment is immediate.
     *
     * The logic of the app will set this date one month or one year in the future.
     */
    public function getNextRenewOn(): \DateTime;

    /**
     * The date on which the feature were subscribed on.
     *
     * @return \DateTime
     */
    public function getSubscribedOn(): \DateTime;

    /**
     * @param string $feature
     *
     * @return bool
     */
    public function has(string $feature): bool;

    /**
     * Shortcut method to check if a Feature in the subscription is still active.
     *
     * @param string $feature
     *
     * @return bool
     */
    public function isStillActive(string $feature): bool;

    /**
     * @param Currency $currency
     *
     * @return SubscriptionInterface
     */
    public function setCurrency(Currency $currency): SubscriptionInterface;

    /**
     * @param SubscribedFeaturesCollection $features
     *
     * @return SubscriptionInterface
     */
    public function setFeatures(SubscribedFeaturesCollection $features): SubscriptionInterface;

    /**
     * @param string $interval
     *
     * @return SubscriptionInterface
     */
    public function setRenewInterval(string $interval): SubscriptionInterface;

    /**
     * @return SubscriptionInterface
     */
    public function setMonthly(): SubscriptionInterface;

    /**
     * @return SubscriptionInterface
     */
    public function setYearly(): SubscriptionInterface;

    /**
     * @param MoneyInterface $amount
     *
     * @return SubscriptionInterface
     */
    public function setNextRenewAmount(MoneyInterface $amount): SubscriptionInterface;

    /**
     * @param \DateTime $nextPaymentOn
     *
     * @return SubscriptionInterface
     */
    public function setNextRenewOn(\DateTime $nextPaymentOn): SubscriptionInterface;

    /**
     * Sets the next payment in one month.
     *
     * @return SubscriptionInterface
     */
    public function setNextPaymentInOneMonth(): SubscriptionInterface;

    /**
     * Sets the next payment in one month.
     *
     * @return SubscriptionInterface
     */
    public function setNextPaymentInOneYear(): SubscriptionInterface;

    /**
     * @param string $refreshInterval
     *
     * @return SubscriptionInterface
     */
    public function setSmallestRefreshInterval(string $refreshInterval): SubscriptionInterface;

    /**
     * @param \DateTime $nextRefreshOn
     *
     * @return SubscriptionInterface
     */
    public function setNextRefreshOn(\DateTime $nextRefreshOn): SubscriptionInterface;

    /**
     * Sets the date on which the feature were subscribed.
     *
     * @param \DateTime $subscribedOn
     *
     * @return SubscriptionInterface
     */
    public function setSubscribedOn(\DateTime $subscribedOn): SubscriptionInterface;

    /**
     * If a feature changes, call this method to force Doctrine to intercept the modification and update the Entity.
     *
     * This is required as Features are stored in a Collection that doesn't change when a Feature is updated.
     * Due to the way Doctrine evaluates changes in entities, this when a Feature changes it doesn't intercept them and
     * so doesn't update the field.
     *
     * Call this method whenever you change a Feature (consuming it or anything else that requires to be updated in the
     * database).
     */
    public function forceFeaturesUpdate();
}
