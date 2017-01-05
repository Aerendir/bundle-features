<?php

namespace SerendipityHQ\Bundle\FeaturesBundle\Model;

/**
 * {@inheritdoc}
 */
class SubscribedRechargeableFeature extends AbstractFeature implements SubscribedRechargeableFeatureInterface
{
    /** @var  int $remainedQuantity The amount of free units of this feature recharged each time */
    private $remainedQuantity;

    /** @var  \DateTime $lastRecharge The last time a recharge was done */
    private $lastRechargeOn;

    /**
     * {@inheritdoc}
     */
    public function __construct(string $name, array $details = [])
    {
        // Set the type
        $details['type'] = self::RECHARGEABLE;

        $this->remainedQuantity = $details['remained_quantity'] ?? 0;
        $this->lastRechargeOn   = $details['last_recharge_on'] ?? new \DateTime;

        if (!$this->lastRechargeOn instanceof \DateTime)
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
    public function getRemainedQuantity() : int
    {
        return $this->remainedQuantity;
    }

    /**
     * {@inheritdoc}
     */
    public function setRemainedQuantity(int $remainedQuantity) : SubscribedRechargeableFeatureInterface
    {
        $this->remainedQuantity = $remainedQuantity;
        $this->lastRechargeOn = new \DateTime();

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
        ], parent::toArray());
    }
}
