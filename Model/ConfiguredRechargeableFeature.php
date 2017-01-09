<?php

namespace SerendipityHQ\Bundle\FeaturesBundle\Model;

use SerendipityHQ\Bundle\FeaturesBundle\Property\CanBeFreeProperty;
use SerendipityHQ\Bundle\FeaturesBundle\Property\HasConfiguredPacksInterface;
use SerendipityHQ\Bundle\FeaturesBundle\Property\HasConfiguredPacksProperty;
use SerendipityHQ\Bundle\FeaturesBundle\Property\HasUnatantumPricesProperty;

/**
 * {@inheritdoc}
 */
class ConfiguredRechargeableFeature extends AbstractFeature implements ConfiguredRechargeableFeatureInterface
{
    use HasConfiguredPacksProperty {
        HasConfiguredPacksProperty::setPacks as setPacksProperty;
    }
    use HasUnatantumPricesProperty;
    use CanBeFreeProperty;

    /** @var  int $freeRecharge The amount of free units of this feature recharged each time */
    private $freeRecharge;

    /**
     * {@inheritdoc}
     */
    public function __construct(string $name, array $details = [])
    {
        // Set the type
        $details['type'] = self::RECHARGEABLE;

        $this->freeRecharge = $details['free_recharge'];

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
    public function setPacks(array $packs, string $class = null) : HasConfiguredPacksInterface
    {
        return $this->setPacksProperty($packs, ConfiguredCountableFeaturePack::class);
    }
}
