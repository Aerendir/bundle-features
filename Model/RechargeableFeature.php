<?php

namespace SerendipityHQ\Bundle\FeaturesBundle\Model;

use SerendipityHQ\Bundle\FeaturesBundle\Property\SimplePricesProperty;

/**
 * {@inheritdoc}
 */
class RechargeableFeature extends AbstractFeature implements RechargeableFeatureInterface
{
    use SimplePricesProperty;

    /** @var  int $freeRecharge The amount of free units of this feature recharged each time */
    private $freeRecharge;

    /** @var  array $packs */
    private $packs;

    /**
     * {@inheritdoc}
     */
    public function __construct(string $name, array $details = [])
    {
        // Set the type
        $details['type'] = self::RECHARGEABLE;

        $this->freeRecharge = $details['free_recharge'] ?? 0;

        if (isset($details['packs']))
            $this->setPacks($details['packs']);

        if (isset($details['unitary_prices']))
            $this->setPrices($details['unitary_prices']);

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
    public function setFreeRecharge(int $freeRecharge) : RechargeableFeatureInterface
    {
        $this->freeRecharge = $freeRecharge;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setPacks(array $packs) : RechargeableFeatureInterface
    {
        foreach ($packs as $numOfUnits => $prices) {
            $this->packs[(int) $numOfUnits] = new RechargeableFeaturePack($numOfUnits, $prices);
        }

        return $this;
    }
}
