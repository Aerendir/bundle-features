<?php

namespace SerendipityHQ\Bundle\FeaturesBundle\Model;

use SerendipityHQ\Bundle\FeaturesBundle\Property\HasRecurringFeatureProperty;

/**
 * {@inheritdoc}
 */
class SubscribedCountableFeature extends AbstractSubscribedFeature implements SubscribedCountableFeatureInterface
{
    use HasRecurringFeatureProperty {
        HasRecurringFeatureProperty::__construct as RecurringFeatureConstruct;
    }

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
