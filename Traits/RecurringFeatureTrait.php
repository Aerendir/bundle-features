<?php

namespace SerendipityHQ\Bundle\FeaturesBundle\Traits;

use SerendipityHQ\Bundle\FeaturesBundle\Model\FeatureInterface;
use SerendipityHQ\Bundle\FeaturesBundle\Model\Subscription;
use SerendipityHQ\Bundle\FeaturesBundle\Model\SubscriptionInterface;
use SerendipityHQ\Component\ValueObjects\Currency\Currency;
use SerendipityHQ\Component\ValueObjects\Currency\CurrencyInterface;
use SerendipityHQ\Component\ValueObjects\Money\Money;
use SerendipityHQ\Component\ValueObjects\Money\MoneyInterface;

/**
 * Common properties and methods of a RecurringFeatureInterface.
 *
 * @method isFromConfiguration() Is contained in AbstractFeature
 */
trait RecurringFeatureTrait
{
    /** @var \DateTime $activeUntil */
    private $activeUntil;

    /** @var array $instantPrices */
    private $instantPrices = [];

    /** @var array $prices */
    private $prices = [];

    /** @var \DateTime $subscribedOn */
    private $subscribedOn;

    /**
     * @param array $details
     */
    public function __construct(array $details = [])
    {
        if (isset($details['active_until'])) {
            $this->activeUntil = $details['active_until'] instanceof \DateTime ? $details['active_until'] : new \DateTime($details['active_until']['date'], new \DateTimeZone($details['active_until']['timezone']));
        }

        if (isset($details['prices'])) {
            $this->setPrices($details['prices']);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getActiveUntil()
    {
        return $this->activeUntil;
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
    public function getPrice($currency, string $subscriptionInterval)
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
        if (false === $this->isFromConfiguration()) {
            throw new \LogicException('You cannot get all prices, a single price or calculate instant prices from a Feature loaded from a subscription object and this is loaded from one of it. Use only Feature objects loaded directly from configuration files.');
        }

        return $this->prices;
    }

    /**
     * {@inheritdoc}
     */
    public function getSubscribedOn() : \DateTime
    {
        return $this->subscribedOn;
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
     * {@inheritdoc}
     */
    public function isStillActive() : bool
    {
        if (null === $this->getActiveUntil()) {
            return false;
        }

        return $this->getActiveUntil() >= new \DateTime();
    }

    /**
     * {@inheritdoc}
     */
    public function setSubscribedOn(\DateTime $subscribedOn) : FeatureInterface
    {
        $this->subscribedOn = $subscribedOn;

        /** @var FeatureInterface $this */
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setActiveUntil(\DateTime $activeUntil) : FeatureInterface
    {
        $this->activeUntil = $activeUntil;

        /** @var FeatureInterface $this */
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
     */
    private function calculateInstantPrice(string $currency, string $subscriptionInterval) : MoneyInterface
    {
        $price = $this->getPrice($currency, $subscriptionInterval);

        // If the feature is not already subscribed or if it was subscribed today
        if (null === $this->subscribedOn || ($this->subscribedOn->format('Y-m-d') === (new \DateTime())->format('Y-m-d'))) {
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
        $remainingDays = clone $this->activeUntil;

        $remainingDays->diff(new \DateTime());

        /* @var \DateInterval $remainingDays */
        $instantPrice = $pricePerDay * $remainingDays->days;

        return new Money(['amount' => $instantPrice, 'currency' => $currency]);
    }
}
