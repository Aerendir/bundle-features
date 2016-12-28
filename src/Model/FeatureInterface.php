<?php

namespace SerendipityHQ\Bundle\FeaturesBundle\Model;

use SerendipityHQ\Component\ValueObjects\Currency\Currency;
use SerendipityHQ\Component\ValueObjects\Currency\CurrencyInterface;
use SerendipityHQ\Component\ValueObjects\Money\MoneyInterface;

/**
 * Common interface for all type of feature.
 */
interface FeatureInterface
{
    const BOOLEAN = 'boolean';
    const RECHARGEABLE = 'rechargeable';

    /**
     * FeatureInterface constructor.
     * @param string $name
     * @param array $details
     */
    public function __construct(string $name, array $details = []);

    /**
     * @return FeatureInterface
     */
    public function disable() : FeatureInterface;

    /**
     * @return FeatureInterface
     */
    public function enable() : FeatureInterface;

    /**
     * The date until which the feature is active.
     *
     * @return \DateTime
     */
    public function getActiveUntil();

    /**
     * @return string
     */
    public function getName() : string;

    /**
     * @param string|Currency $currency This is not typecasted so the method can be called from inside Twig templates simply passing a string.
     * @param string $subscriptionInterval
     *
     * @throws \InvalidArgumentException If the $subscriptionInterval does not exist
     *
     * @return MoneyInterface|null if the price is not set in the required currency
     */
    public function getInstantPrice($currency, string $subscriptionInterval) : MoneyInterface;

    /**
     * @param string|Currency $currency This is not typecasted so the method can be called from inside Twig templates simply passing a string.
     * @param string $subscriptionInterval
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
     * @return string
     */
    public function getType() : string;

    /**
     * @param CurrencyInterface $currency
     * @param string $subscriptionInterval
     *
     * @throws \InvalidArgumentException If the $subscriptionInterval does not exist
     *
     * @return bool
     */
    public function hasPrice(CurrencyInterface $currency, string $subscriptionInterval) : bool;

    /**
     * @return bool
     */
    public function isEnabled() : bool;

    /**
     * @return bool
     */
    public function isStillActive() : bool;

    /**
     * Sets the date until which the feature is active.
     *
     * @param \DateTime $nextPaymentOn
     * @return FeatureInterface
     */
    public function setActiveUntil(\DateTime $nextPaymentOn) : FeatureInterface;

    /**
     * Sets the date on which the feature were subscribed.
     *
     * @param \DateTime $subscribedOn
     * @return FeatureInterface
     */
    public function setSubscribedOn(\DateTime $subscribedOn) : FeatureInterface;

    /**
     * Converts a Feature object into an array.
     *
     * @return array
     */
    public function toArray();
}
