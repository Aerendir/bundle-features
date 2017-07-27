<?php

namespace SerendipityHQ\Bundle\FeaturesBundle\Model;

use Doctrine\ORM\Mapping as ORM;
use SerendipityHQ\Component\ValueObjects\Currency\Currency;
use SerendipityHQ\Component\ValueObjects\Currency\CurrencyInterface;
use SerendipityHQ\Component\ValueObjects\Money\Money;
use SerendipityHQ\Component\ValueObjects\Money\MoneyInterface;

/**
 * Basic properties and methods to manage a subscription.
 *
 * @ORM\MappedSuperclass
 * @ORM\HasLifecycleCallbacks
 */
abstract class Subscription implements SubscriptionInterface
{
    /**
     * @var CurrencyInterface
     *
     * @ORM\Column(name="currency", type="currency", nullable=true)
     */
    private $currency;

    /**
     * Contains the $featuresArray as a FeatureCollection.
     *
     * @var SubscribedFeaturesCollection
     *
     * @ORM\Column(name="features", type="json_array", nullable=true)
     */
    private $features;

    /**
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
     * If there are countable features, this field saves the smallest renew interval found.
     *
     * @var string
     *
     * @ORM\Column(name="smallest_renew_interval", type="string", nullable=true)
     */
    private $smallestRenewInterval;

    /**
     * If there are countable features configured, this field is used to determine when they have to be renew based on
     * the smallest interval.
     *
     * @var \DateTime
     *
     * @ORM\Column(name="next_renew_on", type="datetime", nullable=true)
     */
    private $nextRenewOn;

    /**
     * @var \DateTime $subscribedOn
     *
     * @ORM\Column(name="subscribed_on", type="datetime", nullable=true)
     */
    private $subscribedOn;

    /**
     * {@inheritdoc}
     */
    public function addFeature(string $featureName, FeatureInterface $feature) : SubscriptionInterface
    {
        $this->getFeatures()->set($featureName, $feature);

        return $this;
    }

    /**
     * {@inheritdoc}
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
     * {@inheritdoc}
     */
    public static function checkIntervalExists(string $interval)
    {
        if (false === self::intervalExists($interval)) {
            throw new \InvalidArgumentException(sprintf('The time interval "%s" does not exist. Use SubscriptionInterface to get the right options.', $interval));
        }
    }

    /**
     * {@inheritdoc}
     */
    public static function intervalExists(string $interval) : bool
    {
        return in_array($interval, [
            SubscriptionInterface::DAILY,
            SubscriptionInterface::WEEKLY,
            SubscriptionInterface::BIWEEKLY,
            SubscriptionInterface::MONTHLY,
            SubscriptionInterface::YEARLY
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrency()
    {
        if (null === $this->currency)
            $this->currency = new Currency('EUR');

        return $this->currency;
    }

    /**
     * {@inheritdoc}
     */
    public function getFeatures() : SubscribedFeaturesCollection
    {
        return $this->features;
    }

    /**
     * {@inheritdoc}
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
     * {@inheritdoc}
     */
    public function getNextPaymentAmount() : MoneyInterface
    {
        if (null === $this->nextPaymentAmount)
            $this->nextPaymentAmount = new Money(['amount' => 0, 'currency' => $this->getCurrency()]);

        return $this->nextPaymentAmount;
    }

    /**
     * {@inheritdoc}
     */
    public function getNextPaymentOn() : \DateTime
    {
        if (null === $this->nextPaymentOn) {
            $this->nextPaymentOn = self::calculateActiveUntil($this->getInterval());
        }

        return $this->nextPaymentOn;
    }

    /**
     * @return null|string
     */
    public function getSmallestRenewInterval() :? string
    {
        return $this->smallestRenewInterval;
    }

    /**
     * @return \DateTime|null
     */
    public function getNextRenewOn() :? \DateTime
    {
        return $this->nextRenewOn;
    }

    /**
     * {@inheritdoc}
     */
    public function getSubscribedOn() : \DateTime
    {
        if (null === $this->subscribedOn)
            $this->subscribedOn = new \DateTime();

        return $this->subscribedOn;
    }

    /**
     * {@inheritdoc}
     */
    public function has(string $feature) : bool
    {
        if (0 >= count($this->getFeatures())) {
            return false;
        }

        return $this->getFeatures()->containsKey($feature);
    }

    /**
     * {@inheritdoc}
     */
    public function isStillActive(string $feature) : bool
    {
        if (false === $this->has($feature)) {
            return false;
        }

        return $this->getFeatures()->get($feature)->isStillActive();
    }

    /**
     * {@inheritdoc}
     */
    public function setCurrency(CurrencyInterface $currency) : SubscriptionInterface
    {
        $this->currency = $currency;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setFeatures(SubscribedFeaturesCollection $features) : SubscriptionInterface
    {
        $this->features = $features;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setInterval(string $interval) : SubscriptionInterface
    {
        self::intervalExists($interval);

        $this->interval = $interval;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setMonthly() : SubscriptionInterface
    {
        $this->setInterval(SubscriptionInterface::MONTHLY);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setYearly() : SubscriptionInterface
    {
        $this->setInterval(SubscriptionInterface::YEARLY);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setNextPaymentAmount(MoneyInterface $amount) : SubscriptionInterface
    {
        $this->nextPaymentAmount = $amount;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setNextPaymentOn(\DateTime $nextPaymentOn) : SubscriptionInterface
    {
        $this->nextPaymentOn = $nextPaymentOn;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setNextPaymentInOneMonth() : SubscriptionInterface
    {
        $this->getNextPaymentOn()->modify('+1 month');

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setNextPaymentInOneYear() : SubscriptionInterface
    {
        $this->getNextPaymentOn()->modify('+1 year');

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setSmallestRenewInterval(string $renewInterval) : SubscriptionInterface
    {
        self::intervalExists($renewInterval);

        $this->smallestRenewInterval = $renewInterval;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setNextRenewOn(\DateTime $nextRenewOn) : SubscriptionInterface
    {
        $this->nextRenewOn = $nextRenewOn;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setSubscribedOn(\DateTime $subscribedOn) : SubscriptionInterface
    {
        $this->subscribedOn = $subscribedOn;

        return $this;
    }

    /**
     * @ORM\PostLoad()
     */
    public function hydrateFeatures()
    {
        $this->features = new SubscribedFeaturesCollection($this->features);
    }

    public function __clone()
    {
        $this->features = clone $this->features;
    }
}
