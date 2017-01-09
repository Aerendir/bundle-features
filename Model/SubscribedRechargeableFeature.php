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
    public function getRemainedQuantity() : int
    {
        return $this->remainedQuantity;
    }

    /**
     * {@inheritdoc}
     */
    public function recharge(int $rechargeQuantity) : SubscribedRechargeableFeatureInterface
    {
        $this->remainedQuantity += $rechargeQuantity;
        $this->lastRechargeOn = new \DateTime();
        $this->lastRechargeQuantity = $rechargeQuantity;

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
