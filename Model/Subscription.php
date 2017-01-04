<?php

namespace SerendipityHQ\Bundle\FeaturesBundle\Model;

use Doctrine\ORM\Mapping as ORM;
use SerendipityHQ\Component\ValueObjects\Currency\CurrencyInterface;
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
     * @var FeaturesCollection
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
        return in_array($interval, [SubscriptionInterface::MONTHLY, SubscriptionInterface::YEARLY]);
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * {@inheritdoc}
     */
    public function getFeatures() : FeaturesCollection
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
    public function setFeatures(FeaturesCollection $features) : SubscriptionInterface
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
     * @ORM\PostLoad()
     */
    public function hydrateFeatures()
    {
        $this->features = new FeaturesCollection($this->features);
    }
}