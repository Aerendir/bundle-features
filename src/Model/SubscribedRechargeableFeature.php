<?php

namespace SerendipityHQ\Bundle\FeaturesBundle\Model;

/**
 * {@inheritdoc}
 */
final class SubscribedRechargeableFeature extends AbstractSubscribedFeature implements SubscribedRechargeableFeatureInterface
{
    /** @var  \DateTime $lastRecharge The last time a recharge was done */
    private $lastRechargeOn;

    /** @var  int $lastRechargeQuantity The quantity of units recharged last time */
    private $lastRechargeQuantity;

    /** @var  SubscribedRechargeableFeaturePack */
    private $rechargingPack;

    /** @var  int $remainedQuantity The amount of remained units */
    private $remainedQuantity = 0;

    /**
     * {@inheritdoc}
     */
    public function __construct(string $name, array $details = [])
    {
        // Set the type
        $details['type'] = self::RECHARGEABLE;

        $this->remainedQuantity = $details['remained_quantity'];
        $this->lastRechargeOn   = $details['last_recharge_on'];
        $this->lastRechargeQuantity = $details['last_recharge_quantity'];

        if (null !== $this->lastRechargeOn && !$this->lastRechargeOn instanceof \DateTime)
            $this->lastRechargeOn = new \DateTime($this->lastRechargeOn['date'], new \DateTimeZone($this->lastRechargeOn['timezone']));

        parent::__construct($name, $details);
    }

    /**
     * {@inheritdoc}
     */
    public function getLastRechargeOn() : \DateTime
    {
        return $this->lastRechargeOn;
    }

    /**
     * {@inheritdoc}
     */
    public function getLastRechargeQuantity() : int
    {
        return $this->lastRechargeQuantity;
    }

    /**
     * {@inheritdoc}
     */
    public function getRechargingPack() : SubscribedRechargeableFeaturePack
    {
        if (false === $this->hasRechargingPack()) {
            throw new \LogicException(sprintf('You have not set any rechargin pack so it is not possible to get it or recharge the current rechargin feature "%s"', $this->getName()));
        }

        return $this->rechargingPack;
    }

    /**
     * {@inheritdoc}
     */
    public function getRemainedQuantity() : int
    {
        return $this->remainedQuantity;
    }

    /**
     * {@inheritdoc}
     */
    public function hasRechargingPack() : bool
    {
        return isset($this->rechargingPack);
    }

    /**
     * {@inheritdoc}
     */
    public function recharge() : SubscribedRechargeableFeatureInterface
    {
        $rechargeQuantity = $this->getRechargingPack()->getNumOfUnits();
        $this->remainedQuantity += $rechargeQuantity;
        $this->lastRechargeOn = new \DateTime();
        $this->lastRechargeQuantity = $rechargeQuantity;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setRecharginPack(SubscribedRechargeableFeaturePack $rechargingPack) : SubscribedRechargeableFeatureInterface
    {
        $this->rechargingPack = $rechargingPack;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function toArray()
    {
        return array_merge([
            'remained_quantity' => $this->getRemainedQuantity(),
            'last_recharge_on' => json_decode(json_encode($this->getLastRechargeOn()), true),
            'last_recharge_quantity' => $this->getLastRechargeQuantity()
        ], parent::toArray());
    }
}