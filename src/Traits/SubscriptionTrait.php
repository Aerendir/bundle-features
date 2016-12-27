<?php

namespace SerendipityHQ\Bundle\FeaturesBundle\Traits;

use SebastianBergmann\Money\Money;
use SerendipityHQ\Bundle\FeaturesBundle\Model\FeaturesCollection;
use SerendipityHQ\Bundle\FeaturesBundle\Model\SubscriptionInterface;
use SerendipityHQ\Component\ValueObjects\Currency\Currency;

/**
 * Basic properties and methods to manage a subscription.
 */
trait SubscriptionTrait
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var Currency
     *
     * @ORM\Column(name="currency", type="string", nullable=true)
     */
    private $currency;

    /**
     * @var FeaturesCollection
     *
     * @ORM\Column(name="features", type="json_array", nullable=false)
     */
    private $features;

    /**
     * In number of months.
     *
     * 1 = monthly
     * 12 = yearly
     *
     * @var string
     *
     * @ORM\Column(name="`interval`", type="string", nullable=true)
     */
    private $interval;

    /**
     * @var Money
     *
     * @ORM\Column(name="next_payment_amount", type="money", nullable=true)
     */
    private $nextPaymentAmount;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="next_payment_on", type="datetime", nullable=true)
     */
    private $nextPaymentOn;

    /**
     * @param string $interval
     * @return \DateTime
     */
    public static function calculateActiveUntil(string $interval) : \DateTime
    {
        self::checkIntervalExists($interval);

        $activeUntil = new \DateTime();
        switch ($interval) {
            case SubscriptionInterface::MONTHLY:
                $activeUntil->modify('+1 month');
                break;

            case SubscriptionInterface::YEARLY:
                $activeUntil->modify('+1 year');
                break;
        }

        return $activeUntil;
    }

    /**
     * @param string $interval
     *
     * @throws \InvalidArgumentException If the $interval does not exist
     */
    public static function checkIntervalExists(string $interval)
    {
        if (false === self::intervalExists($interval))
            throw new \InvalidArgumentException(sprintf('The time interval "%s" does not exist. Use SubscriptionInterface to get the right options.', $interval));
    }

    /**
     * @param string $interval
     * @return bool
     */
    public static function intervalExists(string $interval)
    {
        return in_array($interval, [SubscriptionInterface::MONTHLY, SubscriptionInterface::YEARLY]);
    }

    /**
     * Get id.
     *
     * @return int
     */
    public function getId() : int
    {
        return $this->id;
    }

    /**
     * @return FeaturesCollection
     */
    public function getFeatures()
    {
        return $this->features;
    }

    /**
     * Do not set the return typecasting until a currency type is created.
     *
     * @return Currency
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * @return string
     */
    public function getInterval() : string
    {
        if (null === $this->interval) {
            // By default the plan is monthly
            $this->interval = SubscriptionInterface::MONTHLY;
        }

        return $this->interval;
    }

    /**
     * @return Money
     */
    public function getNextPaymentAmount() : Money
    {
        return $this->nextPaymentAmount;
    }

    /**
     * If the date of the next payment is not set, use the creation date.
     * If it is not set, is because this is a new subscription, so the next payment is immediate.
     *
     * The logic of the app will set this date one month or one year in the future.
     *
     * @return \DateTime
     */
    public function getNextPaymentOn()
    {
        /*
        if (null === $this->nextPaymentOn) {
            $this->nextPaymentOn = clone $this->getCreatedOn();
        }
        */

        return $this->nextPaymentOn;
    }

    /**
     * @param int $id
     *
     * @return SubscriptionInterface
     */
    public function setId(int $id) : SubscriptionInterface
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @param Currency $currency
     * @return SubscriptionInterface
     */
    public function setCurrency(Currency $currency) : SubscriptionInterface
    {
        $this->currency = $currency;

        return $this;
    }

    /**
     * @param array $features
     * @return $this
     */
    public function setFeatures(array $features)
    {
        $this->features = $features;

        return $this;
    }

    /**
     * @param string $interval
     *
     * @return SubscriptionInterface
     */
    public function setInterval(string $interval) : SubscriptionInterface
    {
        self::intervalExists($interval);

        $this->interval = $interval;

        return $this;
    }

    /**
     * @return SubscriptionInterface
     */
    public function setMonthly() : SubscriptionInterface
    {
        $this->setInterval(SubscriptionInterface::MONTHLY);

        return $this;
    }

    /**
     * @return SubscriptionInterface
     */
    public function setYearly() : SubscriptionInterface
    {
        $this->setInterval(SubscriptionInterface::YEARLY);

        return $this;
    }

    /**
     * @param Money $amount
     *
     * @return SubscriptionInterface
     */
    public function setNextPaymentAmount(Money $amount) : SubscriptionInterface
    {
        $this->nextPaymentAmount = $amount;

        return $this;
    }

    /**
     * @param \DateTime $nextPaymentOn
     *
     * @return SubscriptionInterface
     */
    public function setNextPaymentOn(\DateTime $nextPaymentOn) : SubscriptionInterface
    {
        $this->nextPaymentOn = $nextPaymentOn;

        return $this;
    }

    /**
     * Sets the next payment in one month.
     *
     * @return SubscriptionInterface
     */
    public function setNextPaymentInOneMonth() : SubscriptionInterface
    {
        $this->getNextPaymentOn()->modify('+1 month');

        return $this;
    }

    /**
     * Sets the next payment in one month.
     *
     * @return SubscriptionInterface
     */
    public function setNextPaymentInTwelveMonths() : SubscriptionInterface
    {
        $this->getNextPaymentOn()->modify('+1 year');

        return $this;
    }

    /**
     * @param string $feature
     * @return bool
     */
    public function has(string $feature) : bool
    {
        return $this->features->containsKey($feature);
    }

    /**
     * @param string $feature
     * @return bool
     */
    public function isStillActive(string $feature) : bool
    {
        if (false === $this->has($feature))
            return false;

        return $this->features->get($feature)->isStillActive();
    }

    /**
     * @return string
     */
    public function __toString() : string
    {
        return (string) $this->getId();
    }

    /**
     * @ORM\PostLoad()
     */
    public function currencyStringToObject()
    {
        if (null === $this->currency)
            return null;

        $this->currency = new Currency($this->currency);
    }

    /**
     * @ORM\PreFlush()
     */
    public function currencyObjectToString()
    {
        if (null === $this->currency)
            return null;

        $this->currency = $this->currency->__toString();
    }

    /**
     * @ORM\PostLoad()
     */
    public function featuresJsonToObject()
    {
        $this->features = new FeaturesCollection($this->features);
    }
}
