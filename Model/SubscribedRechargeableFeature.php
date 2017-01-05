<?php

namespace SerendipityHQ\Bundle\FeaturesBundle\Model;

/**
 * {@inheritdoc}
 */
final class SubscribedRechargeableFeature extends AbstractFeature implements SubscribedRechargeableFeatureInterface
{
    /** @var  int $remainedQuantity The amount of free units of this feature recharged each time */
    private $remainedQuantity;

    /** @var  \DateTime $lastRecharge The last time a recharge was done */
    private $lastRechargeOn;

    /** @var  int $lastRechargeQuantity The quantity of units recharged last time */
    private $lastRechargeQuantity;

    /** @var  int $previousRemainedQuantity Internal variable used when cumulate() is called */
    private $previousRemainedQuantity;

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
        $this->previousRemainedQuantity = $this->remainedQuantity;
        $this->remainedQuantity = $rechargeQuantity;
        $this->lastRechargeOn = new \DateTime();
        $this->lastRechargeQuantity = $rechargeQuantity;

        return $this;
    }

    /**
     * Adds the new recharge amount to the already existent quantity.
     *
     * So, if the current quantity is 4 and a recharge(5) is made, the new $remainedQuantity is 5.
     * But if cumulate() is called, the new $remainedQuantity is 9:
     *
     *     ($previousRemainedQuantity = 4) + ($rechargeQuantity = 5).
     *
     * @return SubscribedRechargeableFeatureInterface
     */
    public function cumulate() : SubscribedRechargeableFeatureInterface
    {
        $this->remainedQuantity += $this->previousRemainedQuantity;

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
