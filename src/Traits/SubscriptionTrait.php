<?php

namespace SerendipityHQ\Bundle\FeaturesBundle\Traits;


use Doctrine\ORM\Mapping as ORM;
use SerendipityHQ\Bundle\FeaturesBundle\Model\FeatureInterface;
use SerendipityHQ\Bundle\FeaturesBundle\Model\FeaturesCollection;
use SerendipityHQ\Bundle\FeaturesBundle\Model\SubscriptionInterface;
use SerendipityHQ\Component\ValueObjects\Currency\CurrencyInterface;
use SerendipityHQ\Component\ValueObjects\Money\MoneyInterface;

/**
 * Basic properties and methods to manage a subscription.
 *
 * This trait can be used only in a SubscriptionInterface object.
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
     * @var CurrencyInterface
     *
     * @ORM\Column(name="currency", type="currency", nullable=true)
     */
    private $currency;

    /**
     * @var FeaturesCollection
     *
     * @ORM\Column(name="features", type="json_array", nullable=true)
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
     * @var MoneyInterface
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
     * @param string $featureName
     * @param FeatureInterface $feature
     * @return SubscriptionInterface
     */
    public function addFeature(string $featureName, FeatureInterface $feature) : SubscriptionInterface
    {
        if (is_array($this->features))
            $this->featuresArrayToCollectionPostLoad();
        $this->features->set($featureName, $feature);

        /** @var SubscriptionInterface $this */
        return $this;
    }

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
     * @return CurrencyInterface
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
     * @return MoneyInterface
     */
    public function getNextPaymentAmount() : MoneyInterface
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
        if (null === $this->nextPaymentOn) {
            $this->nextPaymentOn = new \DateTime();
        }

        return $this->nextPaymentOn;
    }

    /**
     * @param string $feature
     * @return bool
     */
    public function has(string $feature) : bool
    {
        if (0 >= count($this->features))
            return false;

        if (is_array($this->features))
            $this->featuresArrayToCollectionPostLoad();

        return $this->features->containsKey($feature);
    }

    /**
     * @param int $id
     *
     * @return SubscriptionInterface
     */
    public function setId(int $id) : SubscriptionInterface
    {
        $this->id = $id;

        /** @var SubscriptionInterface $this */
        return $this;
    }

    /**
     * @param CurrencyInterface $currency
     * @return SubscriptionInterface
     */
    public function setCurrency(CurrencyInterface $currency) : SubscriptionInterface
    {
        $this->currency = $currency;

        /** @var SubscriptionInterface $this */
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

        /** @var SubscriptionInterface $this */
        return $this;
    }

    /**
     * @return SubscriptionInterface
     */
    public function setMonthly() : SubscriptionInterface
    {
        $this->setInterval(SubscriptionInterface::MONTHLY);

        /** @var SubscriptionInterface $this */
        return $this;
    }

    /**
     * @return SubscriptionInterface
     */
    public function setYearly() : SubscriptionInterface
    {
        $this->setInterval(SubscriptionInterface::YEARLY);

        /** @var SubscriptionInterface $this */
        return $this;
    }

    /**
     * @param MoneyInterface $amount
     *
     * @return SubscriptionInterface
     */
    public function setNextPaymentAmount(MoneyInterface $amount) : SubscriptionInterface
    {
        $this->nextPaymentAmount = $amount;

        /** @var SubscriptionInterface $this */
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

        /** @var SubscriptionInterface $this */
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

        /** @var SubscriptionInterface $this */
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

        /** @var SubscriptionInterface $this */
        return $this;
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
     * @ORM\PostUpdate()
     * @ORM\PostPersist()
     */
    public function featuresArrayToCollectionPostLoad()
    {
        $this->features = new FeaturesCollection($this->features);
    }

    /**
     * @ORM\PreFlush()
     */
    public function featuresObjectToArray()
    {
        if (null === $this->features)
            return [];

        if (is_array($this->features))
            $this->featuresArrayToCollectionPostLoad();

        $this->features = $this->features->toArray();
    }
}
