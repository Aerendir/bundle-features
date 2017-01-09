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

    /** @var  int $subscribedPack */
    private $subscribedPack;

    /** @var int $consumedQuantity How many units are consumed at this time */
    private $consumedQuantity = 0;

    /** @var int $remaining The num of units remained from the last subscription cycle */
    private $remainedQuantity;

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

        $this->subscribedPack = $details['subscribed_pack'];
        $this->remainedQuantity = $details['remained_quantity'];

        if (isset($details['consumed_quantity'])) {
            $this->consumedQuantity = $details['consumed_quantity'];
        }

        parent::__construct($name, $details);
    }

    /**
     * Method to consume the given quantity of this feature.
     *
     * @param int $quantity
     * @return SubscribedCountableFeatureInterface
     */
    public function consume(int $quantity) : SubscribedCountableFeatureInterface
    {
        $this->consumedQuantity += $quantity;
        $this->remainedQuantity -= $quantity;

        return $this;
    }

    /**
     * Method to consume one unit of this feature.
     *
     * @return SubscribedCountableFeatureInterface
     */
    public function consumeOne() : SubscribedCountableFeatureInterface
    {
        return $this->consume(1);
    }

    /**
     * Adds the previous remained amount to the refreshed subscription quantity.
     *
     * So, if the current quantity is 4 and a recharge(5) is made, the new $remainedQuantity is 5.
     * But if cumulate() is called, the new $remainedQuantity is 9:
     *
     *     ($previousRemainedQuantity = 4) + ($rechargeQuantity = 5).
     *
     * @return SubscribedCountableFeatureInterface
     */
    public function cumulate() : SubscribedCountableFeatureInterface
    {
        if (null === $this->previousRemainedQuantity)
            throw new \LogicException('You cannot use cumulate() before refreshing the subscription with refresh().');

        $this->remainedQuantity = $this->getRemainedQuantity() + $this->previousRemainedQuantity;

        return $this;
    }

    /**
     * @return int
     */
    public function getConsumedQuantity() : int
    {
        return $this->consumedQuantity;
    }

    /**
     * @return int
     */
    public function getRemainedQuantity() : int
    {
        return $this->remainedQuantity;
    }

    /**
     * @return int|ConfiguredCountableFeaturePack
     */
    public function getSubscribedPack()
    {
        return $this->subscribedPack;
    }

    /**
     * Transforms the $subscribedPack integer into the correspondent ConfiguredFeaturePackInterface object.
     *
     * {@inheritdoc}
     */
    public function setConfiguredFeature(ConfiguredFeatureInterface $configuredFeature)
    {
        /** @var ConfiguredCountableFeatureInterface $configuredFeature */
        $configuredPack = $configuredFeature->getPack($this->subscribedPack);
        $this->subscribedPack = $configuredPack;

        parent::setConfiguredFeature($configuredFeature);
    }

    /**
     * At the end of the subscription period, use this method to refresh the quantities.
     */
    public function refreshSubscription() : SubscribedCountableFeatureInterface
    {
        $this->previousRemainedQuantity = $this->getRemainedQuantity();

        $this->consumedQuantity = 0;
        $this->remainedQuantity = $this->getSubscribedPack()->getNumOfUnits();

        return $this;
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
            'subscribed_pack' => $subscribedPack,
            'remained_quantity' => $this->getRemainedQuantity(),
            'consumed_quantity' => $this->getConsumedQuantity()
        ], parent::toArray());
    }
}
