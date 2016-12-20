<?php

namespace SerendipityHQ\Bundle\FeaturesBundle\Model;

use SebastianBergmann\Money\Money;

interface SubscriptionInterface
{
    /**
     * Get id.
     *
     * @return int
     */
    public function getId() : int;

    /**
     * @return \DateTime
     */
    public function getCreatedOn() : \DateTime;

    /**
     * @return int
     */
    public function getInterval() : int ;

    /**
     * @return Money
     */
    public function getNextPaymentAmount() : Money;

    /**
     * If the date of the next payment is not set, use the creation date.
     * If it is not set, is because this is a new subscription, so the next payment is immediate.
     *
     * The logic of the app will set this date one month or one year in the future.
     *
     * @return \DateTime
     */
    public function getNextPaymentOn() : \DateTime;

    /**
     * @return \DateTime
     */
    public function getUpdatedOn() : \DateTime;

    /**
     * @param int $id
     *
     * @return SubscriptionInterface
     */
    public function setId(int $id) : SubscriptionInterface;

    /**
     * @param int $interval
     *
     * @return SubscriptionInterface
     */
    public function setInterval($interval) : SubscriptionInterface;

    /**
     * @return SubscriptionInterface
     */
    public function setMonthly() : SubscriptionInterface;

    /**
     * @return SubscriptionInterface
     */
    public function setYearly() : SubscriptionInterface;

    /**
     * @param Money $amount
     *
     * @return SubscriptionInterface
     */
    public function setNextPaymentAmount(Money $amount) : SubscriptionInterface;

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
     * @param \DateTime $updatedOn
     *
     * @return SubscriptionInterface
     */
    public function setUpdatedOn(\DateTime $updatedOn) : SubscriptionInterface;

    /**
     * @return string
     */
    public function __toString() : string;
}
