<?php

namespace SerendipityHQ\Bundle\FeaturesBundle\Model;

use SerendipityHQ\Bundle\FeaturesBundle\Traits\SubscriptionTrait;
use SerendipityHQ\Component\ValueObjects\Currency\Currency;
use SerendipityHQ\Component\ValueObjects\Money\Money;

class BooleanFeature extends AbstractFeature implements BooleanFeatureInterface
{
    /** @var  array $prices */
    private $prices = [];

    public function __construct(string $name, array $details = [])
    {
        // Set the type
        $details['type'] = self::BOOLEAN;

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

        parent::__construct($name, $details);
    }

    /**
     * @param string|Currency $currency This is not typecasted so the method can be called from inside Twig templates.
     * @param string $subscriptionInterval
     *
     * @throws \InvalidArgumentException If the $subscriptionInterval does not exist
     *
     * @return Money|null if the price is not set in the required currency
     */
    public function getPrice($currency, string $subscriptionInterval)
    {
        if (is_string($currency))
            $currency = new Currency($currency);

        SubscriptionTrait::checkIntervalExists($subscriptionInterval);

        return $this->getPrices()[$currency->getCurrencyCode()][$subscriptionInterval] ?? null;
    }

    /**
     * @return array
     */
    public function getPrices() : array
    {
        return $this->prices;
    }

    /**
     * @param Currency $currency
     * @param string $subscriptionInterval
     *
     * @throws \InvalidArgumentException If the $subscriptionInterval does not exist
     *
     * @return bool
     */
    public function hasPrice(Currency $currency, string $subscriptionInterval) : bool
    {
        SubscriptionTrait::checkIntervalExists($subscriptionInterval);

        return isset($this->getPrices()[$currency->getCurrencyCode()][$subscriptionInterval]);
    }
}
