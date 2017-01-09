<?php

namespace SerendipityHQ\Bundle\FeaturesBundle\Property;

use SerendipityHQ\Bundle\FeaturesBundle\Model\Subscription;
use SerendipityHQ\Bundle\FeaturesBundle\Model\SubscriptionInterface;
use SerendipityHQ\Component\ValueObjects\Currency\Currency;
use SerendipityHQ\Component\ValueObjects\Currency\CurrencyInterface;
use SerendipityHQ\Component\ValueObjects\Money\Money;
use SerendipityHQ\Component\ValueObjects\Money\MoneyInterface;

/**
 * Common properties and methods of a ConfiguredRecurringFeatureInterface.
 *
 * @method isFromConfiguration() Is contained in AbstractFeature
 */
trait HasRecurringPricesProperty
{
    /** @var array $instantPrices */
    private $instantPrices = [];

    /** @var array $prices */
    private $prices = [];

    /** @var  SubscriptionInterface $subscription */
    private $subscription;

    /**
     * @param array $details
     */
    public function __construct(array $details = [])
    {
        if (isset($details['price'])) {
            $this->setPrices($details['price']);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getInstantPrice($currency, string $subscriptionInterval) : MoneyInterface
    {
        if ($currency instanceof CurrencyInterface) {
            $currency = $currency->getCurrencyCode();
        }

        if (false === isset($this->instantPrices[$currency][$subscriptionInterval])) {
            $this->instantPrices[$currency][$subscriptionInterval] = $this->calculateInstantPrice($currency, $subscriptionInterval);
        }

        return $this->instantPrices[$currency][$subscriptionInterval];
    }

    /**
     * {@inheritdoc}
     */
    public function getPrice($currency, string $subscriptionInterval) : MoneyInterface
    {
        if ($currency instanceof CurrencyInterface) {
            $currency = $currency->getCurrencyCode();
        }

        Subscription::checkIntervalExists($subscriptionInterval);

        return $this->getPrices()[$currency][$subscriptionInterval] ?? new Money(['amount' => 0, 'currency' => new Currency($currency)]);
    }

    /**
     * {@inheritdoc}
     */
    public function getPrices() : array
    {
        return $this->prices;
    }

    /**
     * {@inheritdoc}
     */
    public function hasPrice($currency, string $subscriptionInterval) : bool
    {
        Subscription::checkIntervalExists($subscriptionInterval);

        if ($currency instanceof CurrencyInterface) {
            $currency = $currency->getCurrencyCode();
        }

        return isset($this->getPrices()[$currency][$subscriptionInterval]);
    }

    /**
     * @param SubscriptionInterface $subscription
     * @return HasRecurringPricesInterface
     */
    public function setSubscription(SubscriptionInterface $subscription) : HasRecurringPricesInterface
    {
        $this->subscription = $subscription;

        /** @var HasRecurringPricesInterface $this */
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    protected function setPrices(array $prices)
    {
        if (0 < count($prices)) {
            foreach ($prices as $currency => $price) {
                $currency = new Currency($currency);

                if (isset($price[SubscriptionInterface::MONTHLY])) {
                    $amount = $price[SubscriptionInterface::MONTHLY];
                    if (!$amount instanceof MoneyInterface) {
                        $amount = new Money([
                            'amount' => $price[SubscriptionInterface::MONTHLY], 'currency' => $currency,
                        ]);
                    }
                    $this->prices[$currency->getCurrencyCode()][SubscriptionInterface::MONTHLY] = $amount;
                }

                if (isset($price[SubscriptionInterface::YEARLY])) {
                    $amount = $price[SubscriptionInterface::YEARLY];
                    if (!$amount instanceof MoneyInterface) {
                        $amount = new Money([
                            'amount' => $price[SubscriptionInterface::YEARLY], 'currency' => $currency,
                        ]);
                    }
                    $this->prices[$currency->getCurrencyCode()][SubscriptionInterface::YEARLY] = $amount;
                }
            }
        }
    }

    /**
     * @param string $currency
     * @param string $subscriptionInterval
     *
     * @return MoneyInterface
     *
     * @todo pass directly a Subscription object.
     */
    private function calculateInstantPrice(string $currency, string $subscriptionInterval) : MoneyInterface
    {
        if (!$this->subscription instanceof SubscriptionInterface)
            throw new \RuntimeException('Before you can get instant prices you have to set a Subscription with setSubscription().');

        $price = $this->getPrice($currency, $subscriptionInterval);

        // If the feature is not already subscribed or if it was subscribed today
        if (null === $this->subscription->getSubscribedOn() || ($this->subscription->getSubscribedOn()->format('Y-m-d') === (new \DateTime())->format('Y-m-d'))) {
            // ...the user has never paid, so he has no remaining days of subscription and has to pay the full price
            return $price;
        }

        switch ($subscriptionInterval) {
            case SubscriptionInterface::MONTHLY:
                // Our ideal month is ever of 31 days
                $daysInInterval = 31;
                break;
            case SubscriptionInterface::YEARLY:
                // Our ideal year is ever of 365 days
                $daysInInterval = 365;
                break;
            default:
                throw new \InvalidArgumentException(sprintf('The subscription interval can be only "%s" or "%s". "%s" passed.', SubscriptionInterface::MONTHLY, SubscriptionInterface::YEARLY, $subscriptionInterval));
        }

        $pricePerDay = (int) floor($price->getAmount() / $daysInInterval);

        // Calculate the remaining days
        $remainingDays = clone $this->getActiveUntil();

        /** @var \DateTime $remainingDays */
        $remainingDays->diff(new \DateTime());

        /* @var \DateInterval $remainingDays */
        $instantPrice = $pricePerDay * $remainingDays->days;

        return new Money(['amount' => $instantPrice, 'currency' => $currency]);
    }
}
