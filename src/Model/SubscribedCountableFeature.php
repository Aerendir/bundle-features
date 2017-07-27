<?php

namespace SerendipityHQ\Bundle\FeaturesBundle\Model;

use SerendipityHQ\Bundle\FeaturesBundle\Property\IsRecurringFeatureProperty;

/**
 * {@inheritdoc}
 */
class SubscribedCountableFeature extends AbstractSubscribedFeature implements SubscribedCountableFeatureInterface
{
    use IsRecurringFeatureProperty {
        IsRecurringFeatureProperty::__construct as RecurringFeatureConstruct;
    }

    /** @var \DateTime $lastRenewOn */
    private $lastRenewOn;

    /** @var  SubscribedCountableFeaturePack $subscribedPack */
    private $subscribedPack;

    /** @var int $consumedQuantity How many units are consumed at this time */
    private $consumedQuantity = 0;

    /** @var int $remaining The num of units remained from the last subscription cycle */
    private $remainedQuantity = 0;

    /** @var int $previousRemainedQuantity Internally used by cumulate() */
    private $previousRemainedQuantity = 0;

    /**
     * {@inheritdoc}
     */
    public function __construct(string $name, array $details = [])
    {
        // Set the type
        $details['type'] = self::COUNTABLE;

        $this->RecurringFeatureConstruct($details);

        $this->subscribedPack = new SubscribedCountableFeaturePack($details['subscribed_pack']);
        $this->remainedQuantity = $details['remained_quantity'];

        if (isset($details['last_renew_on'])) {
            $this->lastRenewOn = $details['last_renew_on'] instanceof \DateTime ? $details['last_renew_on'] : new \DateTime($details['last_renew_on']['date'], new \DateTimeZone($details['last_renew_on']['timezone']));
        }

        parent::__construct($name, $details);
    }

    /**
     * {@inheritdoc}
     */
    public function consume(int $quantity) : SubscribedCountableFeatureInterface
    {
        $this->consumedQuantity += $quantity;
        $this->remainedQuantity -= $quantity;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function consumeOne() : SubscribedCountableFeatureInterface
    {
        return $this->consume(1);
    }

    /**
     * {@inheritdoc}
     */
    public function cumulate() : SubscribedCountableFeatureInterface
    {
        if (null === $this->previousRemainedQuantity)
            throw new \LogicException('You cannot use cumulate() before refreshing the subscription with refresh().');

        $this->remainedQuantity = $this->getRemainedQuantity() + $this->previousRemainedQuantity;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getConsumedQuantity() : int
    {
        return $this->consumedQuantity;
    }

    /**
     * {@inheritdoc}
     */
    public function getLastRenewOn() : \DateTime
    {
        return $this->lastRenewOn;
    }

    /**
     * {@inheritdoc}
     */
    public function getRemainedQuantity() : int
    {
        return $this->remainedQuantity;
    }

    /**
     * @return SubscribedCountableFeaturePack
     */
    public function getSubscribedPack() : SubscribedCountableFeaturePack
    {
        return $this->subscribedPack;
    }

    /**
     * {@inheritdoc}
     */
    public function isRenewPeriodElapsed(): bool
    {
        // We don't have a last renew date:
        if (null === $this->getLastRenewOn()) {
            // Return true by default t force the renew and set the last renew date to be used on the next cycle
            return true;
        }

        /** @var ConfiguredCountableFeatureInterface $configuredFeature */
        $configuredFeature = $this->getConfiguredFeature();

        $now = new \DateTime();

        $diff = $now->diff($this->getLastRenewOn());

        switch ($configuredFeature->getRenewPeriod()) {
            case SubscriptionInterface::DAILY:
                return $diff->days >= 1 ? true : false;
                break;
            case SubscriptionInterface::WEEKLY:
                return $diff->days >= 7 ? true : false;
                break;
            case SubscriptionInterface::BIWEEKLY:
                return $diff->days >= 15 ? true : false;
                break;
            case SubscriptionInterface::MONTHLY:
                return $diff->m >= 1 ? true : false;
                break;
            case SubscriptionInterface::YEARLY:
                return $diff->y >= 1 ? true : false;
                break;
        }

        // By default return true
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function refreshSubscription() : SubscribedCountableFeatureInterface
    {
        $this->previousRemainedQuantity = $this->getRemainedQuantity();

        $this->consumedQuantity = 0;
        $this->remainedQuantity = $this->getSubscribedPack()->getNumOfUnits();

        return $this;
    }

    /**
     * Transforms the $subscribedPack integer into the correspondent ConfiguredFeaturePackInterface object.
     *
     * {@inheritdoc}
     */
    public function setConfiguredFeature(ConfiguredFeatureInterface $configuredFeature)
    {
        parent::setConfiguredFeature($configuredFeature);
    }

    /**
     * {@inheritdoc}
     */
    public function setLastRenewOn(\DateTime $lastRenewOn) : SubscribedCountableFeatureInterface
    {
        $this->lastRenewOn = $lastRenewOn;

        /** @var SubscribedCountableFeatureInterface $this */
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setSubscribedPack(SubscribedCountableFeaturePack $pack)
    {
        // The remained quantity we had at the begininning of the subscription period
        $this->remainedQuantity += $this->consumedQuantity;

        // The perevious remained quantity
        $this->remainedQuantity -= $this->subscribedPack->getNumOfUnits();

        // The new remained quantity
        $this->remainedQuantity += $pack->getNumOfUnits();

        // The actual remained quantity
        $this->remainedQuantity -= $this->consumedQuantity;

        // Set the new subscribed pack
        $this->subscribedPack = $pack;
    }

    /**
     * {@inheritdoc}
     */
    public function toArray()
    {
        $subscribedPack = $this->getSubscribedPack();

        // If it is an object, transofmr it
        if ($subscribedPack instanceof ConfiguredCountableFeaturePack) {
            $subscribedPack = $subscribedPack->getNumOfUnits();
        }

        return array_merge([
            'active_until' => json_decode(json_encode($this->getActiveUntil()), true),
            'subscribed_pack' => $subscribedPack->toArray(),
            'remained_quantity' => $this->getRemainedQuantity(),
            'consumed_quantity' => $this->getConsumedQuantity()
        ], parent::toArray());
    }
}
