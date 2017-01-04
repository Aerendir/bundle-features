<?php

namespace SerendipityHQ\Bundle\FeaturesBundle\Model;

use SerendipityHQ\Bundle\FeaturesBundle\Property\UnatantumPricesProperty;

/**
 * {@inheritdoc}
 */
class SubscribedRechargeableFeature extends AbstractFeature implements SubscribedRechargeableFeatureInterface
{
    use UnatantumPricesProperty;

    /** @var  int $freeRecharge The amount of free units of this feature recharged each time */
    private $freeRecharge;

    /**
     * {@inheritdoc}
     */
    public function __construct(string $name, array $details = [])
    {
        // Set the type
        $details['type'] = self::RECHARGEABLE;

        $this->freeRecharge = $details['free_recharge'] ?? 0;

        parent::__construct($name, $details);
    }

    /**
     * {@inheritdoc}
     */
    public function getFreeRecharge() : int
    {
        return $this->freeRecharge;
    }

    /**
     * {@inheritdoc}
     */
    public function setFreeRecharge(int $freeRecharge) : SubscribedRechargeableFeatureInterface
    {
        $this->freeRecharge = $freeRecharge;

        return $this;
    }
}
