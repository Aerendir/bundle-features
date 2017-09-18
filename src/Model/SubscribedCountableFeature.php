<?php

/*
 * This file is part of the SHQFeaturesBundle.
 *
 * Copyright Adamo Aerendir Crespi 2016-2017.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    Adamo Aerendir Crespi <hello@aerendir.me>
 * @copyright Copyright (C) 2016 - 2017 Aerendir. All rights reserved.
 * @license   MIT License.
 */

namespace SerendipityHQ\Bundle\FeaturesBundle\Model;

use SerendipityHQ\Bundle\FeaturesBundle\Property\CanBeConsumedProperty;
use SerendipityHQ\Bundle\FeaturesBundle\Property\IsRecurringFeatureProperty;

/**
 * {@inheritdoc}
 */
class SubscribedCountableFeature extends AbstractSubscribedFeature implements SubscribedCountableFeatureInterface
{
    use IsRecurringFeatureProperty {
        IsRecurringFeatureProperty::__construct as RecurringFeatureConstruct;
    }
    use CanBeConsumedProperty;

    /** @var int $previousRemainedQuantity Internally used by cumulate() */
    private $previousRemainedQuantity = 0;

    /** @var \DateTime $lastRefreshOn */
    private $lastRefreshOn;

    /** @var SubscribedCountableFeaturePack $subscribedPack */
    private $subscribedPack;

    /**
     * {@inheritdoc}
     */
    public function __construct(string $name, array $details = [])
    {
        // Set the type
        $details['type'] = self::COUNTABLE;

        $this->RecurringFeatureConstruct($details);

        $this->subscribedPack = new SubscribedCountableFeaturePack($details['subscribed_pack']);
        $this->setRemainedQuantity($details['remained_quantity']);

        // If is null we need to set it to NOW
        if (null === $this->lastRefreshOn) {
            $this->lastRefreshOn = new \DateTime();
        }

        // If we have it passed as a detail (from the database), then we use it
        if (isset($details['last_refresh_on'])) {
            $this->lastRefreshOn = $details['last_refresh_on'] instanceof \DateTime ? $details['last_refresh_on'] : new \DateTime($details['last_refresh_on']['date'], new \DateTimeZone($details['last_refresh_on']['timezone']));
        }

        parent::__construct($name, $details);
    }

    /**
     * {@inheritdoc}
     */
    public function cumulate(): SubscribedCountableFeatureInterface
    {
        if (null === $this->previousRemainedQuantity) {
            throw new \LogicException('You cannot use cumulate() before refreshing the subscription with refresh().');
        }
        $this->remainedQuantity = $this->getRemainedQuantity() + $this->previousRemainedQuantity;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getLastRefreshOn(): ? \DateTime
    {
        return $this->lastRefreshOn;
    }

    /**
     * {@inheritdoc}
     */
    public function getRemainedQuantity(): int
    {
        return $this->remainedQuantity;
    }

    /**
     * @return SubscribedCountableFeaturePack
     */
    public function getSubscribedPack(): SubscribedCountableFeaturePack
    {
        return $this->subscribedPack;
    }

    /**
     * {@inheritdoc}
     */
    public function isRefreshPeriodElapsed(): bool
    {
        // We don't have a last renew date:
        if (null === $this->getLastRefreshOn()) {
            // Return true by default t force the renew and set the last renew date to be used on the next cycle
            return true;
        }

        /** @var ConfiguredCountableFeatureInterface $configuredFeature */
        $configuredFeature = $this->getConfiguredFeature();

        $now = new \DateTime();

        $diff = $now->diff($this->getLastRefreshOn());

        switch ($configuredFeature->getRefreshPeriod()) {
            case SubscriptionInterface::DAILY:
                return $diff->days >= 1 ? true : false;
                break;
            case SubscriptionInterface::WEEKLY:
                return $diff->days >= 7 ? true : false;
                break;
            case SubscriptionInterface::BIWEEKLY:
                return $diff->days >= 15 ? true : false;
                break;
            case SubscriptionInterface::MONTHLY:
                return $diff->m >= 1 ? true : false;
                break;
            case SubscriptionInterface::YEARLY:
                return $diff->y >= 1 ? true : false;
                break;
        }

        // By default return true
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function refresh(): SubscribedCountableFeatureInterface
    {
        $this->previousRemainedQuantity = $this->getRemainedQuantity();

        $this->consumedQuantity = 0;
        $this->remainedQuantity = $this->getSubscribedPack()->getNumOfUnits();

        // Set the last refresh on to NOW
        $this->lastRefreshOn = new \DateTime();

        return $this;
    }

    /**
     * Transforms the $subscribedPack integer into the correspondent ConfiguredFeaturePackInterface object.
     *
     * {@inheritdoc}
     */
    public function setConfiguredFeature(ConfiguredFeatureInterface $configuredFeature)
    {
        parent::setConfiguredFeature($configuredFeature);
    }

    /**
     * {@inheritdoc}
     */
    public function setLastRefreshOn(\DateTime $lastRefreshOn): SubscribedCountableFeatureInterface
    {
        $this->lastRefreshOn = $lastRefreshOn;

        /** @var SubscribedCountableFeatureInterface $this */
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setSubscribedPack(SubscribedCountableFeaturePack $pack)
    {
        // The remained quantity we had at the begininning of the subscription period
        $this->remainedQuantity += $this->consumedQuantity;

        // The perevious remained quantity
        $this->remainedQuantity -= $this->subscribedPack->getNumOfUnits();

        // The new remained quantity
        $this->remainedQuantity += $pack->getNumOfUnits();

        // The actual remained quantity
        $this->remainedQuantity -= $this->consumedQuantity;

        // Set the new subscribed pack
        $this->subscribedPack = $pack;
    }

    /**
     * {@inheritdoc}
     */
    public function toArray()
    {
        $subscribedPack = $this->getSubscribedPack();

        // If it is an object, transofmr it
        if ($subscribedPack instanceof ConfiguredCountableFeaturePack) {
            $subscribedPack = $subscribedPack->getNumOfUnits();
        }

        return array_merge([
            'active_until'    => json_decode(json_encode($this->getActiveUntil()), true),
            'subscribed_pack' => $subscribedPack->toArray(),
            'last_refresh_on' => json_decode(json_encode($this->getLastRefreshOn()), true),
        ], parent::toArray(), $this->consumedToArray());
    }
}
