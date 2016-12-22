<?php

namespace SerendipityHQ\Bundle\FeaturesBundle\Traits;

use SebastianBergmann\Money\Money;
use SerendipityHQ\Bundle\FeaturesBundle\Model\SubscriptionInterface;

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
     * @var \DateTime
     *
     * @ORM\Column(name="created_on", type="datetime", nullable=false)
     */
    private $createdOn;

    /**
     * @var string
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
     * @var int
     *
     * @ORM\Column(name="`interval`", type="integer", nullable=false)
     */
    private $interval = 1;

    /**
     * @var Money
     *
     * @ORM\Column(name="next_payment_amount", type="money", nullable=false)
     */
    private $nextPaymentAmount;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="next_payment_on", type="datetime", nullable=false)
     */
    private $nextPaymentOn;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_on", type="datetime", nullable=false)
     */
    private $updatedOn;

    /**
     * Get id.
     *
     * @return int
     */
    public function getId() : int
    {
        return $this->id;
    }

    public function getFeatures()
    {
        return $this->features;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedOn() : \DateTime
    {
        return $this->createdOn;
    }

    /**
     * @return int
     */
    public function getInterval() : int
    {
        if (null === $this->interval) {
            // By default the plan is monthly
            $this->interval = 1;
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
    public function getNextPaymentOn() : \DateTime
    {
        if (null === $this->nextPaymentOn) {
            $this->nextPaymentOn = clone $this->getCreatedOn();
        }

        return $this->nextPaymentOn;
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedOn() : \DateTime
    {
        return $this->updatedOn;
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

    public function setFeatures(array $features)
    {
        $this->features = $features;

        return $this;
    }

    /**
     * @param int $interval
     *
     * @return SubscriptionInterface
     */
    public function setInterval($interval) : SubscriptionInterface
    {
        if (false === array_search($interval, [1, 12])) {
            throw new \InvalidArgumentException('The interval MUST have a value of (int) 1 or (int) 12.');
        }

        $this->interval = $interval;

        return $this;
    }

    /**
     * @return SubscriptionInterface
     */
    public function setMonthly() : SubscriptionInterface
    {
        $this->setInterval(1);

        return $this;
    }

    /**
     * @return SubscriptionInterface
     */
    public function setYearly() : SubscriptionInterface
    {
        $this->setInterval(12);

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
     * @param \DateTime $updatedOn
     *
     * @return SubscriptionInterface
     */
    public function setUpdatedOn(\DateTime $updatedOn) : SubscriptionInterface
    {
        $this->updatedOn = $updatedOn;

        return $this;
    }

    /**
     * @return string
     */
    public function __toString() : string
    {
        return (string) $this->getId();
    }
}
