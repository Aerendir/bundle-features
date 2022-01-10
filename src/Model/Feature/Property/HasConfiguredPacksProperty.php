<?php

/*
 * This file is part of the Serendipity HQ Features Bundle.
 *
 * Copyright (c) Adamo Aerendir Crespi <aerendir@serendipityhq.com>.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SerendipityHQ\Bundle\FeaturesBundle\Model\Feature\Property;

use function Safe\sprintf;
use SerendipityHQ\Bundle\FeaturesBundle\Model\Feature\Configured\ConfiguredCountableFeaturePack;
use SerendipityHQ\Bundle\FeaturesBundle\Model\Feature\Configured\ConfiguredFeaturePackInterface;
use SerendipityHQ\Bundle\FeaturesBundle\Model\Feature\Configured\ConfiguredRechargeableFeaturePack;

/**
 * Manages packs of a Feature.
 */
trait HasConfiguredPacksProperty
{
    /** @var array $packs */
    private $packs;

    /**
     * {@inheritdoc}
     */
    public function getPack(int $numOfUnits): ?ConfiguredFeaturePackInterface
    {
        return $this->hasPack($numOfUnits) ? $this->packs[$numOfUnits] : null;
    }

    /**
     * {@inheritdoc}
     */
    public function getPacks(): array
    {
        return $this->packs;
    }

    public function hasPack(int $numOfUnits): bool
    {
        return isset($this->packs[$numOfUnits]);
    }

    /**
     * {@inheritdoc}
     */
    public function setPacks(array $packs, string $packClass): HasConfiguredPacksInterface
    {
        $pricesType = $packs[HasConfiguredPacksInterface::_PRICES_TYPES];
        unset($packs[HasConfiguredPacksInterface::_PRICES_TYPES]);

        foreach ($packs as $numOfUnits => $prices) {
            switch ($packClass) {
                case ConfiguredRechargeableFeaturePack::class:
                case ConfiguredCountableFeaturePack::class:
                    /** @var ConfiguredFeaturePackInterface $pack */
                    $pack = new $packClass($numOfUnits, $prices, $pricesType);

                    break;
                default:
                    throw new \RuntimeException(sprintf('Class "%s" reached the default condition in the switch and this is not managed.', $packClass));
            }

            // If the subscription is set, set it in the pack, too (maybe the pack doesn't have a subscription property, so check for it)
            if ($pack instanceof HasRecurringPricesInterface && (property_exists($this, 'subscription') && null !== $this->subscription) && null !== $this->subscription) {
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
