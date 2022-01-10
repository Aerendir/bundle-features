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
use SerendipityHQ\Bundle\FeaturesBundle\Model\Feature\FeatureInterface;
use SerendipityHQ\Bundle\FeaturesBundle\Model\Feature\Subscribed\SubscribedFeaturesCollection;
use SerendipityHQ\Component\ValueObjects\Money\MoneyInterface;

/**
 * Interface for a Subscription.
 */
interface SubscriptionInterface
{
    public const DAILY    = 'daily';
    public const WEEKLY   = 'weekly';
    public const BIWEEKLY = 'biweekly';
    public const MONTHLY  = 'monthly';
    public const YEARLY   = 'yearly';

    /**
     * @return \DateTime|\DateTimeImmutable
     */
    public static function calculateActiveUntil(string $interval): \DateTimeInterface;

    /**
     * @throws \InvalidArgumentException If the $interval does not exist
     */
    public static function checkIntervalExists(string $interval);

    public static function intervalExists(string $interval): bool;

    public function addFeature(string $featureName, FeatureInterface $feature): SubscriptionInterface;

    /**
     * Do not set the return typecasting until a currency type is created.
     */
    public function getCurrency(): Currency;

    /**
     * Forces the features to be returned as a ConfiguredFeaturesCollection.
     */
    public function getFeatures(): SubscribedFeaturesCollection;

    /**
     * Get the current subscription interval.
     *
     * By default it is set to "monthly".
     */
    public function getRenewInterval(): string;

    public function getNextRenewAmount(): MoneyInterface;

    public function getSmallestRefreshInterval(): ?string;

    /**
     * @return \DateTime|\DateTimeImmutable|null
     */
    public function getNextRefreshOn(): ?\DateTimeInterface;

    /**
     * If the date of the next payment is not set, use the creation date.
     * If it is not set, is because this is a new subscription, so the next payment is immediate.
     *
     * The logic of the app will set this date one month or one year in the future.
     *
     * @return \DateTime|\DateTimeImmutable
     */
    public function getNextRenewOn(): \DateTimeInterface;

    /**
     * The date on which the feature were subscribed on.
     *
     * @return \DateTime|\DateTimeImmutable
     */
    public function getSubscribedOn(): \DateTimeInterface;

    public function has(string $feature): bool;

    /**
     * Shortcut method to check if a Feature in the subscription is still active.
     */
    public function isStillActive(string $feature): bool;

    public function setCurrency(Currency $currency): SubscriptionInterface;

    public function setFeatures(SubscribedFeaturesCollection $features): SubscriptionInterface;

    public function setRenewInterval(string $interval): SubscriptionInterface;

    public function setMonthly(): SubscriptionInterface;

    public function setYearly(): SubscriptionInterface;

    public function setNextRenewAmount(MoneyInterface $amount): SubscriptionInterface;

    /**
     * @param \DateTime|\DateTimeImmutable $nextPaymentOn
     */
    public function setNextRenewOn(\DateTimeInterface $nextPaymentOn): SubscriptionInterface;

    /**
     * Sets the next payment in one month.
     */
    public function setNextPaymentInOneMonth(): SubscriptionInterface;

    /**
     * Sets the next payment in one month.
     */
    public function setNextPaymentInOneYear(): SubscriptionInterface;

    public function setSmallestRefreshInterval(string $refreshInterval): SubscriptionInterface;

    /**
     * @param \DateTime|\DateTimeImmutable $nextRefreshOn
     */
    public function setNextRefreshOn(\DateTimeInterface $nextRefreshOn): SubscriptionInterface;

    /**
     * Sets the date on which the feature were subscribed.
     *
     * @param \DateTime|\DateTimeImmutable $subscribedOn
     */
    public function setSubscribedOn(\DateTimeInterface $subscribedOn): SubscriptionInterface;

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
