<?php

namespace SerendipityHQ\Bundle\FeaturesBundle\Model;

use SerendipityHQ\Bundle\FeaturesBundle\Property\RecurringFeatureProperty;

/**
 * {@inheritdoc}
 */
class SubscribedRechargeableFeature extends AbstractFeature implements SubscribedRechargeableFeatureInterface
{
    use RecurringFeatureProperty {
        RecurringFeatureProperty::__construct as RecurringFeatureConstruct;
    }

    /** @var  int $rechargeAmount The amount of free units of this feature recharged each time */
    private $rechargeAmount;

    /**
     * {@inheritdoc}
     */
    public function __construct(string $name, array $details = [])
    {
        // Set the type
        $details['type'] = self::RECHARGEABLE;

        $this->rechargeAmount = $details['recharge_amount'] ?? 0;

        $this->RecurringFeatureConstruct($details);
        parent::__construct($name, $details);
    }

    /**
     * {@inheritdoc}
     */
    public function getRechargeAmount() : int
    {
        return $this->rechargeAmount;
    }

    /**
     * {@inheritdoc}
     */
    public function setRechargeAmount(int $rechargeAmount) : SubscribedRechargeableFeatureInterface
    {
        $this->rechargeAmount = $rechargeAmount;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function toArray()
    {
        return array_merge([
            'active_until' => json_decode(json_encode($this->getActiveUntil()), true),
            'recharge_amount' => $this->getRechargeAmount()
        ], parent::toArray());
    }
}
