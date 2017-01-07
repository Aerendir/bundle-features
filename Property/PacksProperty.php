<?php

namespace SerendipityHQ\Bundle\FeaturesBundle\Property;

use SerendipityHQ\Bundle\FeaturesBundle\Model\ConfiguredFeaturePackInterface;

/**
 * Manages packs of a Feature.
 */
trait PacksProperty
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
     * @return bool
     */
    public function hasFreePack() : bool
    {
        return null === $this->freePack;
    }

    /**
     * {@inheritdoc}
     */
    public function setPacks(array $packs, string $packClass) : HasPacksInterface
    {
        foreach ($packs as $numOfUnits => $prices) {
            /** @var ConfiguredFeaturePackInterface $pack */
            $pack = new $packClass($numOfUnits, $prices);

            // If the subscription is set, set it in the pack, too
            if ($pack instanceof HasRecurringPricesInterface && null !== $this->subscription) {
                $pack->setSubscription($this->subscription);
            }

            // If the current pack is the free one, set it as free
            if ($pack->isFree()) {
                $this->freePack = $pack;
            }


            $this->packs[$numOfUnits] = $pack;
        }

        /** @var HasPacksInterface $this */
        return $this;
    }
}
