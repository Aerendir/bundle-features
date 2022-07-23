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

use SerendipityHQ\Bundle\FeaturesBundle\Model\Feature\Configured\ConfiguredCountableFeature;
use SerendipityHQ\Bundle\FeaturesBundle\Model\Feature\Configured\ConfiguredCountableFeaturePack;
use SerendipityHQ\Bundle\FeaturesBundle\Model\Feature\FeatureInterface;
use SerendipityHQ\Bundle\FeaturesBundle\Model\Feature\Property\CanBeConsumedInterface;
use SerendipityHQ\Bundle\FeaturesBundle\Model\Feature\Property\CanBeConsumedProperty;
use SerendipityHQ\Bundle\FeaturesBundle\Model\Feature\Property\IsRecurringFeatureInterface;
use SerendipityHQ\Bundle\FeaturesBundle\Model\Feature\Property\IsRecurringFeatureProperty;
use SerendipityHQ\Bundle\FeaturesBundle\Model\SubscriptionInterface;

use function Safe\json_decode;
use function Safe\json_encode;

final class SubscribedCountableFeature extends AbstractSubscribedFeature implements SubscribedFeatureInterface, IsRecurringFeatureInterface, CanBeConsumedInterface
{
    use IsRecurringFeatureProperty {
        IsRecurringFeatureProperty::__construct as RecurringFeatureConstruct;
    }
    use CanBeConsumedProperty;

    public const FIELD_LAST_REFRESH_ON         = 'last_refresh_on';
    public const FIELD_SUBSCRIBED_PACK         = 'subscribed_pack';
    public const FIELD_SUBSCRIBED_NUM_OF_UNITS = 'num_of_units';

    /** @var int $previousRemainedQuantity Internally used by cumulate() */
    private int $previousRemainedQuantity = 0;

    private \DateTimeInterface $lastRefreshOn;
    private SubscribedCountableFeaturePack $subscribedPack;

    public function __construct(string $name, array $details = [])
    {
        // Set the type
        $details[FeatureInterface::FIELD_TYPE] = self::TYPE_COUNTABLE;

        $this->RecurringFeatureConstruct($details);

        $this->subscribedPack = new SubscribedCountableFeaturePack($details[SubscribedCountableFeature::FIELD_SUBSCRIBED_PACK]);
        $this->setRemainedQuantity($details[SubscribedCountableFeature::REMAINED_QUANTITY]);

        // If is null we need to set it to NOW
        if (null === $this->lastRefreshOn) {
            $this->lastRefreshOn = new \DateTime();
        }

        // If we have it passed as a detail (from the database), then we use it
        if (isset($details[self::FIELD_LAST_REFRESH_ON])) {
            $this->lastRefreshOn = $details[self::FIELD_LAST_REFRESH_ON] instanceof \DateTime ? $details[self::FIELD_LAST_REFRESH_ON] : new \DateTime($details[self::FIELD_LAST_REFRESH_ON]['date'], new \DateTimeZone($details[self::FIELD_LAST_REFRESH_ON]['timezone']));
        }

        parent::__construct($name, $details);
    }

    /**
     * Adds the previous remained amount to the refreshed subscription quantity.
     *
     * So, if the current quantity is 4 and a recharge(5) is made, the new $remainedQuantity is 5.
     * But if cumulate() is called, the new $remainedQuantity is 9:
     *
     *     ($previousRemainedQuantity = 4) + ($rechargeQuantity = 5).
     */
    public function cumulate(): self
    {
        if (null === $this->previousRemainedQuantity) {
            throw new \LogicException('You cannot use cumulate() before refreshing the subscription with refresh().');
        }

        $this->remainedQuantity = $this->getRemainedQuantity() + $this->previousRemainedQuantity;

        return $this;
    }

    /**
     * The date on which the feature were renew last time.
     *
     * This can return null so it is compatible with older versions of the Bundle.
     */
    public function getLastRefreshOn(): \DateTimeInterface
    {
        return $this->lastRefreshOn;
    }

    public function getRemainedQuantity(): int
    {
        return $this->remainedQuantity;
    }

    /**
     * It is an integer when the feature is loaded from the database.
     *
     * Then, once called FeaturesManager::setSubscription(), this is transformed into the correspondent
     * ConfiguredFeaturePackInterface object.
     */
    public function getSubscribedPack(): SubscribedCountableFeaturePack
    {
        return $this->subscribedPack;
    }

    /**
     * Checks if the refresh period is elapsed for this feature.
     */
    public function isRefreshPeriodElapsed(): bool
    {
        // We don't have a last renew date:
        if (null === $this->getLastRefreshOn()) {
            // Return true by default t force the renew and set the last renew date to be used on the next cycle
            return true;
        }

        /** @var ConfiguredCountableFeature $configuredFeature */
        $configuredFeature = $this->getConfiguredFeature();

        $now = new \DateTime();

        $diff = $now->diff($this->getLastRefreshOn());

        switch ($configuredFeature->getRefreshPeriod()) {
            case SubscriptionInterface::DAILY:
                return $diff->days >= 1;
            case SubscriptionInterface::WEEKLY:
                return $diff->days >= 7;
            case SubscriptionInterface::BIWEEKLY:
                return $diff->days >= 15;
            case SubscriptionInterface::MONTHLY:
                return $diff->m >= 1;
            case SubscriptionInterface::YEARLY:
                return $diff->y >= 1;
        }

        // By default return true
        return true;
    }

    /**
     * Renews the subscription resetting the available quantities.
     */
    public function refresh(): self
    {
        $this->previousRemainedQuantity = $this->getRemainedQuantity();

        $this->consumedQuantity = 0;
        $this->remainedQuantity = $this->getSubscribedPack()->getNumOfUnits();

        // Set the last refresh on to NOW
        $this->lastRefreshOn = new \DateTime();

        return $this;
    }

    /**
     * Sets the date on which the renew happened.
     *
     * @param \DateTime|\DateTimeImmutable $lastRefreshOn
     */
    public function setLastRefreshOn(\DateTimeInterface $lastRefreshOn): self
    {
        $this->lastRefreshOn = $lastRefreshOn;

        return $this;
    }

    public function setSubscribedPack(SubscribedCountableFeaturePack $pack): void
    {
        // The remained quantity we had at the beginning of the subscription period
        $this->remainedQuantity += $this->consumedQuantity;

        // The previous remained quantity
        $this->remainedQuantity -= $this->subscribedPack->getNumOfUnits();

        // The new remained quantity
        $this->remainedQuantity += $pack->getNumOfUnits();

        // The actual remained quantity
        $this->remainedQuantity -= $this->consumedQuantity;

        // Set the new subscribed pack
        $this->subscribedPack = $pack;
    }

    public function toArray(): array
    {
        $subscribedPack = $this->getSubscribedPack();

        // If it is an object, transform it
        if ($subscribedPack instanceof ConfiguredCountableFeaturePack) {
            $subscribedPack = $subscribedPack->getNumOfUnits();
        }

        return \array_merge([
            IsRecurringFeatureInterface::FIELD_ACTIVE_UNTIL    => json_decode(json_encode($this->getActiveUntil(), JSON_THROW_ON_ERROR), true, 512, JSON_THROW_ON_ERROR),
            self::FIELD_SUBSCRIBED_PACK                        => $subscribedPack->toArray(),
            self::FIELD_LAST_REFRESH_ON                        => json_decode(json_encode($this->getLastRefreshOn(), JSON_THROW_ON_ERROR), true, 512, JSON_THROW_ON_ERROR),
        ], parent::toArray(), $this->consumedToArray());
    }
}
