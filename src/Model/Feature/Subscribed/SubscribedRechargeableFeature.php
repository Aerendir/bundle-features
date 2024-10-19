<?php

declare(strict_types=1);

/*
 * This file is part of the Serendipity HQ Features Bundle.
 *
 * Copyright (c) Adamo Aerendir Crespi <aerendir@serendipityhq.com>.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SerendipityHQ\Bundle\FeaturesBundle\Model\Feature\Subscribed;

use SerendipityHQ\Bundle\FeaturesBundle\Model\Feature\Property\CanBeConsumedInterface;
use SerendipityHQ\Bundle\FeaturesBundle\Model\Feature\Property\CanBeConsumedProperty;

use function Safe\json_decode;
use function Safe\json_encode;

final class SubscribedRechargeableFeature extends AbstractSubscribedFeature implements SubscribedFeatureInterface, CanBeConsumedInterface
{
    use CanBeConsumedProperty;

    /** @var \DateTimeInterface $lastRecharge The last time a recharge was done */
    private \DateTimeInterface $lastRechargeOn;

    /** @var int $lastRechargeQuantity The quantity of units recharged last time */
    private int $lastRechargeQuantity;

    private SubscribedRechargeableFeaturePack $rechargingPack;

    /** @var int $remainedQuantity The amount of remained units */
    private int $remainedQuantity = 0;

    public function __construct(string $name, array $details = [])
    {
        // Set the type
        $details[self::FIELD_TYPE] = self::TYPE_RECHARGEABLE;

        if (false === array_key_exists('remained_quantity', $details) || null === $details['remained_quantity']) {
            throw new \InvalidArgumentException('The key "remained_quantity" is mandatory for a subscribed rechargeable feature. You must provide it.');
        }

        if (false === array_key_exists('last_recharge_on', $details) || null === $details['last_recharge_on']) {
            throw new \InvalidArgumentException('The key "last_recharge_on" is mandatory for a subscribed rechargeable feature. You must provide it.');
        }

        if (false === array_key_exists('last_recharge_quantity', $details) || null === $details['last_recharge_quantity']) {
            throw new \InvalidArgumentException('The key "last_recharge_quantity" is mandatory for a subscribed rechargeable feature. You must provide it.');
        }

        $this->setRemainedQuantity($details['remained_quantity']);
        $this->setLastRechargedOn($details['last_recharge_on']);
        $this->lastRechargeQuantity = $details['last_recharge_quantity'];

        parent::__construct($name, $details);
    }

    /**
     * @return \DateTime|\DateTimeImmutable
     */
    public function getLastRechargeOn(): \DateTimeInterface
    {
        return $this->lastRechargeOn;
    }

    public function getLastRechargeQuantity(): int
    {
        return $this->lastRechargeQuantity;
    }

    public function getRechargingPack(): SubscribedRechargeableFeaturePack
    {
        if (false === $this->hasRechargingPack()) {
            throw new \LogicException(sprintf('You have not set any rechargin pack so it is not possible to get it or recharge the current rechargin feature "%s"', $this->getName()));
        }

        return $this->rechargingPack;
    }

    public function hasRechargingPack(): bool
    {
        return isset($this->rechargingPack);
    }

    public function recharge(): SubscribedRechargeableFeature
    {
        $rechargeQuantity = $this->getRechargingPack()->getNumOfUnits();
        $this->remainedQuantity += $rechargeQuantity;
        $this->lastRechargeOn       = new \DateTime();
        $this->lastRechargeQuantity = $rechargeQuantity;

        return $this;
    }

    public function setRechargingPack(SubscribedRechargeableFeaturePack $rechargingPack): SubscribedRechargeableFeature
    {
        $this->rechargingPack = $rechargingPack;

        return $this;
    }

    public function toArray(): array
    {
        return \array_merge([
            'last_recharge_on'       => json_decode(json_encode($this->getLastRechargeOn(), JSON_THROW_ON_ERROR), true, 512, JSON_THROW_ON_ERROR),
            'last_recharge_quantity' => $this->getLastRechargeQuantity(),
        ], parent::toArray(), $this->consumedToArray());
    }

    private function setLastRechargedOn(\DateTimeInterface|array $lastRechargeOn): SubscribedRechargeableFeature
    {
        if (is_array($lastRechargeOn)) {
            $lastRechargeOn = new \DateTime($lastRechargeOn['date'], new \DateTimeZone($lastRechargeOn['timezone']));
        }
        $this->lastRechargeOn = $lastRechargeOn;

        return $this;
    }
}
