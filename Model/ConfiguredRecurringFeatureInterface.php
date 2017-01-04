<?php

namespace SerendipityHQ\Bundle\FeaturesBundle\Model;
use SerendipityHQ\Component\ValueObjects\Currency\CurrencyInterface;
use SerendipityHQ\Component\ValueObjects\Money\MoneyInterface;

/**
 * {@inheritdoc}
 */
interface ConfiguredRecurringFeatureInterface extends FeatureInterface
{
    /**
     * The date until which the feature is active.
     *
     * @return \DateTime
     */
    public function getActiveUntil();

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
     * @param string|CurrencyInterface $currency             This is not typecasted so the method can be called from inside Twig templates simply passing a string
     * @param string          $subscriptionInterval
     *
     * @throws \InvalidArgumentException If the $subscriptionInterval does not exist
     *
     * @return MoneyInterface|null if the price is not set in the required currency
     */
    public function getPrice($currency, string $subscriptionInterval);

    /**
     * @return array
     */
    public function getPrices() : array;

    /**
     * The date on which the feature were subscribed on.
     *
     * @return \DateTime
     */
    public function getSubscribedOn() : \DateTime;

    /**
     * @param CurrencyInterface|string $currency
     * @param string            $subscriptionInterval
     *
     * @throws \InvalidArgumentException If the $subscriptionInterval does not exist
     *
     * @return bool
     */
    public function hasPrice($currency, string $subscriptionInterval) : bool;

    /**
     * @return bool
     */
    public function isStillActive() : bool;

    /**
     * Sets the date until which the feature is active.
     *
     * @param \DateTime $nextPaymentOn
     *
     * @return FeatureInterface
     */
    public function setActiveUntil(\DateTime $nextPaymentOn) : FeatureInterface;

    /**
     * Sets the date on which the feature were subscribed.
     *
     * @param \DateTime $subscribedOn
     *
     * @return FeatureInterface
     */
    public function setSubscribedOn(\DateTime $subscribedOn) : FeatureInterface;
}
