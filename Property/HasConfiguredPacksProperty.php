<?php

namespace SerendipityHQ\Bundle\FeaturesBundle\Property;

use SerendipityHQ\Bundle\FeaturesBundle\Model\ConfiguredFeaturePackInterface;

/**
 * Manages packs of a Feature.
 */
trait HasConfiguredPacksProperty
{
    /** @var  array $packs */
    private $packs;

    /** @var  ConfiguredFeaturePackInterface $freePack */
    private $freePack;

    /**
     * @return ConfiguredFeaturePackInterface
     */
    public function getFreePack() : ConfiguredFeaturePackInterface
    {
        return $this->freePack;
    }

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
     * @return bool
     */
    public function hasFreePack() : bool
    {
        return null !== $this->freePack;
    }

    /**
     * {@inheritdoc}
     */
    public function setPacks(array $packs, string $packClass) : HasConfiguredPacksInterface
    {
        foreach ($packs as $numOfUnits => $prices) {
            /** @var ConfiguredFeaturePackInterface $pack */
            $pack = new $packClass($numOfUnits, $prices);

            // If the subscription is set, set it in the pack, too (maybe the pack doesn't have a subscription property, so check for it)
            if ($pack instanceof HasRecurringPricesInterface && isset($this->subscription) && null !== $this->subscription) {
                $pack->setSubscription($this->subscription);
            }

            // If the current pack is the free one, set it as free
            if ($pack->isFree()) {
                $this->freePack = $pack;
            }


            $this->packs[$numOfUnits] = $pack;
        }

        /** @var HasConfiguredPacksInterface $this */
        return $this;
    }
}
