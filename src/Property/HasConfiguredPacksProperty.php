<?php

namespace SerendipityHQ\Bundle\FeaturesBundle\Property;

use SerendipityHQ\Bundle\FeaturesBundle\Model\ConfiguredCountableFeaturePack;
use SerendipityHQ\Bundle\FeaturesBundle\Model\ConfiguredFeaturePackInterface;
use SerendipityHQ\Bundle\FeaturesBundle\Model\ConfiguredRechargeableFeaturePack;

/**
 * Manages packs of a Feature.
 */
trait HasConfiguredPacksProperty
{
    /** @var  array $packs */
    private $packs;

    /**
     * @param int $numOfUnits
     * @return null|ConfiguredFeaturePackInterface
     */
    public function getPack(int $numOfUnits)
    {
        return $this->hasPack($numOfUnits) ? $this->packs[$numOfUnits] : null;
    }

    /**
     * @return array
     */
    public function getPacks() : array
    {
        return $this->packs;
    }

    /**
     * @param int $numOfUnits
     * @return bool
     */
    public function hasPack(int $numOfUnits) : bool
    {
        return isset($this->packs[$numOfUnits]);
    }

    /**
     * {@inheritdoc}
     */
    public function setPacks(array $packs, string $packClass) : HasConfiguredPacksInterface
    {
        $pricesType = $packs['_pricesType'];
        unset($packs['_pricesType']);

        foreach ($packs as $numOfUnits => $prices) {
            switch($packClass) {
                case ConfiguredRechargeableFeaturePack::class:
                case ConfiguredCountableFeaturePack::class:
                    /** @var ConfiguredFeaturePackInterface $pack */
                    $pack = new $packClass($numOfUnits, $prices, $pricesType);
                    break;
                default:
                    throw new \RuntimeException(sprintf('Class "%s" reached the default condition in the switch and this is not managed.', $packClass));
            }

            // If the subscription is set, set it in the pack, too (maybe the pack doesn't have a subscription property, so check for it)
            if ($pack instanceof HasRecurringPricesInterface && isset($this->subscription) && null !== $this->subscription) {
                $pack->setSubscription($this->subscription);
            }

            // If the current pack is the free one, set it as free
            if ($this instanceof CanHaveFreePackInterface && $pack instanceof CanBeFreeInterface && $pack->isFree()) {
                $this->setFreePack($pack);
            }

            $this->packs[$numOfUnits] = $pack;
        }

        /** @var HasConfiguredPacksInterface $this */
        return $this;
    }
}
