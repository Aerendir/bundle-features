<?php

namespace SerendipityHQ\Bundle\FeaturesBundle\Model;

use SerendipityHQ\Component\ValueObjects\Currency\Currency;
use SerendipityHQ\Component\ValueObjects\Currency\CurrencyInterface;
use SerendipityHQ\Component\ValueObjects\Money\Money;
use SerendipityHQ\Component\ValueObjects\Money\MoneyInterface;

/**
 * {@inheritdoc}
 */
abstract class AbstractFeature implements FeatureInterface
{
    /** @var bool $fromConfiguration This is set to true only if the feature is loaded from a subscription object */
    private $fromConfiguration = false;

    /** @var  \DateTime $activeUntil */
    private $activeUntil;

    /** @var  bool $enabled */
    private $enabled = false;

    /** @var array $instantPrices */
    private $instantPrices = [];

    /** @var  string $name */
    private $name;

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
            $this->activeUntil = $details['active_until'] instanceof \DateTime ? $details['active_until'] : new \DateTime($details['active_until']['date'], new \DateTimeZone($details['active_until']['timezone']));
        }

        if (isset($details['enabled']) && true === $details['enabled'])
            $this->enable();

        if (isset($details['prices'])) {
            $this->setPrices($details['prices']);
        }

        /*
         * This property defines if the feature is loading from the configuration file or from a subscription object.
         *
         * If it is loaded from a subscription object, in fact, some features, like the instant prices, are disabled.
         */
        if (isset($details['from_configuration'])) {
            $this->fromConfiguration = true;
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
    public function getPrice($currency, string $subscriptionInterval)
    {
        if (is_string($currency))
            $currency = new Currency($currency);

        Subscription::checkIntervalExists($subscriptionInterval);

        return $this->getPrices()[$currency->getCurrencyCode()][$subscriptionInterval] ?? new Money(['amount' => 0, 'currency' => $currency]);
    }

    /**
     * {@inheritdoc}
     */
    public function getPrices() : array
    {
        if (false === $this->fromConfiguration)
            throw new \LogicException('You cannot get all prices, a single price or calculate instant prices from a Feature loaded from a subscription object and this is loaded from one of it. Use only Feature objects loaded directly from configuration files.');

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
        Subscription::checkIntervalExists($subscriptionInterval);

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
    public function setActiveUntil(\DateTime $activeUntil) : FeatureInterface
    {
        $this->activeUntil = $activeUntil;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    private function setPrices(array $prices)
    {
        if (0 < count($prices)) {
            foreach ($prices as $currency => $price) {
                $currency = new Currency($currency);

                if (isset($price[SubscriptionInterface::MONTHLY])) {
                    $amount = $price[SubscriptionInterface::MONTHLY];
                    if (!$amount instanceof MoneyInterface) {
                        $amount = new Money([
                            'amount' => $price[SubscriptionInterface::MONTHLY], 'currency' => $currency
                        ]);
                    }
                    $this->prices[$currency->getCurrencyCode()][SubscriptionInterface::MONTHLY] = $amount;
                }

                if (isset($price[SubscriptionInterface::YEARLY])) {
                    $amount = $price[SubscriptionInterface::YEARLY];
                    if (!$amount instanceof MoneyInterface) {
                        $amount = new Money([
                            'amount' => $price[SubscriptionInterface::YEARLY], 'currency' => $currency
                        ]);
                    }
                    $this->prices[$currency->getCurrencyCode()][SubscriptionInterface::YEARLY] = $amount;
                }
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function toArray()
    {
        return [
            'active_until' => json_decode(json_encode($this->getActiveUntil()), true),
            'type' => $this->getType(),
            'enabled' => $this->isEnabled()
        ];
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

        /** @var \DateInterval $remainingDays */
        $remainingDays->diff(new \DateTime());

        $instantPrice = $pricePerDay * $remainingDays->days;

        return $instantPrice;
    }
}
