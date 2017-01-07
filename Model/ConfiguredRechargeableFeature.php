<?php

namespace SerendipityHQ\Bundle\FeaturesBundle\Model;

use SerendipityHQ\Bundle\FeaturesBundle\Property\UnatantumPricesProperty;

/**
 * {@inheritdoc}
 */
class ConfiguredRechargeableFeature extends AbstractFeature implements ConfiguredRechargeableFeatureInterface
{
    use UnatantumPricesProperty;

    /** @var  bool $cumulable If true, the new recharge is added to the existing quantity. If false, is substituted to the existent quantity */
    private $cumulable;

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

        $this->freeRecharge = $details['free_recharge'];
        $this->cumulable = $details['cumulable'];

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
    public function setFreeRecharge(int $freeRecharge) : ConfiguredRechargeableFeatureInterface
    {
        $this->freeRecharge = $freeRecharge;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setPacks(array $packs) : ConfiguredRechargeableFeatureInterface
    {
        foreach ($packs as $numOfUnits => $prices) {
            $this->packs[$numOfUnits] = new ConfiguredRechargeableFeaturePack($numOfUnits, $prices);
        }

        return $this;
    }
}
