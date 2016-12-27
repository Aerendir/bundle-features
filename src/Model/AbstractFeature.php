<?php

namespace SerendipityHQ\Bundle\FeaturesBundle\Model;

use SerendipityHQ\Bundle\FeaturesBundle\Traits\SubscriptionTrait;
use SerendipityHQ\Component\ValueObjects\Currency\Currency;
use SerendipityHQ\Component\ValueObjects\Currency\CurrencyInterface;
use SerendipityHQ\Component\ValueObjects\Money\Money;
use SerendipityHQ\Component\ValueObjects\Money\MoneyInterface;

/**
 * {@inheritdoc}
 */
abstract class AbstractFeature implements FeatureInterface
{
    /** @var  \DateTime $activeUntil */
    private $activeUntil;

    /** @var  bool $enabled */
    private $enabled = false;

    /** @var array $instantPrices */
    private $instantPrices = [];

    /** @var  string $name */
    private $name;

    /** @var  \DateTime $nextPaymentOn */
    private $nextPaymentOn;

    /** @var  array $prices */
    private $prices = [];

    /** @var  \DateTime $subscribedOn */
    private $subscribedOn;

    /** @var string $type */
    private $type;

    /**
     * {@inheritdoc}
     */
    public function __construct(string $name, array $details = [])
    {
        $this->name = $name;
        $this->disable();

        if (isset($details['active_until'])) {
            $this->activeUntil = new \DateTime($details['active_until']['date'], new \DateTimeZone($details['active_until']['timezone']));
        }

        if (isset($details['enabled']) && true === $details['enabled'])
            $this->enable();

        if (isset($details['prices']) && is_array($details['prices']) && 0 < count($details['prices'])) {
            foreach ($details['prices'] as $currency => $price) {
                $currency = new Currency($currency);

                if (isset($price[SubscriptionInterface::MONTHLY]))
                    $this->prices[$currency->getCurrencyCode()][SubscriptionInterface::MONTHLY] = new Money([
                        'amount' => $price[SubscriptionInterface::MONTHLY], 'currency' => $currency
                    ]);

                if (isset($price[SubscriptionInterface::YEARLY]))
                    $this->prices[$currency->getCurrencyCode()][SubscriptionInterface::YEARLY] = new Money([
                        'amount' => $price[SubscriptionInterface::YEARLY], 'currency' => $currency
                    ]);
            }
        }

        $this->type = $details['type'];
    }

    /**
     * {@inheritdoc}
     */
    public function disable() : FeatureInterface
    {
        $this->enabled = false;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function enable() : FeatureInterface
    {
        $this->enabled = true;

        return $this;
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
    public function getName() : string
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function getInstantPrice($currency, string $subscriptionInterval) : MoneyInterface
    {
        if ($currency instanceof CurrencyInterface)
            $currency = $currency->getCurrencyCode();

        if (false === isset($this->instantPrices[$currency][$subscriptionInterval]))
            $this->instantPrices[$currency][$subscriptionInterval] = $this->calculateInstantPrice($currency, $subscriptionInterval);

        return $this->instantPrices[$currency][$subscriptionInterval];
    }

    /**
     * {@inheritdoc}
     */
    public function getNextPaymentOn()
    {
        return $this->nextPaymentOn;
    }

    /**
     * {@inheritdoc}
     */
    public function getPrice($currency, string $subscriptionInterval)
    {
        if (is_string($currency))
            $currency = new Currency($currency);

        SubscriptionTrait::checkIntervalExists($subscriptionInterval);

        return $this->getPrices()[$currency->getCurrencyCode()][$subscriptionInterval] ?? new Money(['amount' => 0, 'currency' => $currency]);
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
    public function getSubscribedOn() : \DateTime
    {
        return $this->subscribedOn;
    }

    /**
     * {@inheritdoc}
     */
    public function getType() : string
    {
        return $this->type;
    }

    /**
     * {@inheritdoc}
     */
    public function hasPrice(CurrencyInterface $currency, string $subscriptionInterval) : bool
    {
        SubscriptionTrait::checkIntervalExists($subscriptionInterval);

        return isset($this->getPrices()[$currency->getCurrencyCode()][$subscriptionInterval]);
    }

    /**
     * {@inheritdoc}
     */
    public function isEnabled() : bool
    {
        return $this->enabled;
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
     * @return array
     *
    public function getPrices()
    {
        $return = [];

        // Process boolean features
        foreach ($this->getFeaturesHandler()->getFeatures() as $feature => $details) {
            dump($feature, $details);
            // Process prices
            /*
            foreach ($details['price'] as $currency => $prices) {
                $amountMonth = $details['enabled'] ? 0 : $prices['month'];
                $amountYear = $details['enabled'] ? 0 : $prices['year'];
                $return[$feature][$currency]['month'] = new Money(['amount' => $amountMonth, 'currency' => new Currency($currency)]);
                $return[$feature][$currency]['year'] = new Money(['amount' => $amountYear, 'currency' => new Currency($currency)]);
                $instantMont = $this->calculateInstantPrice($this->getSubscription(), $feature);
                $return[$feature][$currency]['instantMonth'] = new Money(['amount' => $instantMont, 'currency' => new Currency($currency)]);
            }
            *
        }
        die;

        return $return;
    }
     */

    /**
     * {@inheritdoc}
     */
    public function setSubscribedOn(\DateTime $subscribedOn) : FeatureInterface
    {
        $this->subscribedOn = $subscribedOn;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setNextPaymentOn(\DateTime $nextPaymentOn) : FeatureInterface
    {
        $this->nextPaymentOn = $nextPaymentOn;

        return $this;
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
                $subscriptionInterval = 1;
                // Our ideal month is ever of 31 days
                $daysInInterval = 31;
                break;
            case SubscriptionInterface::YEARLY:
                $subscriptionInterval = 12;
                // Our ideal year is ever of 365 days
                $daysInInterval = 365;
                break;
            deafult:
                throw new \InvalidArgumentException(sprintf('The subscription interval can be only "%s" or "%s". "%s" passed.', SubscriptionInterface::MONTHLY, SubscriptionInterface::YEARLY, $subscriptionInterval));
        }

        $pricePerDay = (int) floor($price->getAmount() / $daysInInterval);

        // Calculate the remaining days
        $remainingDays = clone $this->nextPaymentOn;

        /** \DateInterval */
        $remainingDays->diff(new \DateTime());

        $instantPrice = $pricePerDay * $remainingDays->days;

        return $instantPrice;
    }
}
