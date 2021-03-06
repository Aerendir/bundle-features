<?php

/*
 * This file is part of the Serendipity HQ Features Bundle.
 *
 * Copyright (c) Adamo Aerendir Crespi <aerendir@serendipityhq.com>.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SerendipityHQ\Bundle\FeaturesBundle\Model;

use Doctrine\ORM\Mapping as ORM;
use Money\Currency;
use SerendipityHQ\Bundle\FeaturesBundle\Model\Feature\FeatureInterface;
use SerendipityHQ\Bundle\FeaturesBundle\Model\Feature\Subscribed\SubscribedFeaturesCollection;
use SerendipityHQ\Component\ValueObjects\Money\Money;
use SerendipityHQ\Component\ValueObjects\Money\MoneyInterface;

/**
 * Basic properties and methods to manage a subscription.
 *
 * @ORM\MappedSuperclass
 * @ORM\HasLifecycleCallbacks
 */
abstract class Subscription implements SubscriptionInterface
{
    /**
     * @var Currency
     *
     * @ORM\Column(name="currency", type="currency", nullable=true)
     */
    private $currency;

    /**
     * Contains the $featuresArray as a FeatureCollection.
     *
     * @var array|SubscribedFeaturesCollection
     *
     * @ORM\Column(name="features", type="json", nullable=true)
     */
    private $features;

    /**
     * @var string
     *
     * @ORM\Column(name="`renew_interval`", type="string", nullable=true)
     */
    private $renewInterval;

    /**
     * @var MoneyInterface
     *
     * @ORM\Column(name="next_renew_amount", type="money", nullable=true)
     */
    private $nextRenewAmount;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="next_renew_on", type="datetime", nullable=true)
     */
    private $nextRenewOn;

    /**
     * If there are countable features, this field saves the smallest refresh interval found.
     *
     * @var string
     *
     * @ORM\Column(name="smallest_refresh_interval", type="string", nullable=true)
     */
    private $smallestRefreshInterval;

    /**
     * If there are countable features configured, this field is used to determine when they have to be refresh based on
     * the smallest interval.
     *
     * @var \DateTime
     *
     * @ORM\Column(name="next_refresh_on", type="datetime", nullable=true)
     */
    private $nextRefreshOn;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="subscribed_on", type="datetime", nullable=true)
     */
    private $subscribedOn;

    /**
     * {@inheritdoc}
     */
    public function addFeature(string $featureName, FeatureInterface $feature): SubscriptionInterface
    {
        $this->getFeatures()->set($featureName, $feature);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public static function calculateActiveUntil(string $interval): \DateTime
    {
        self::checkIntervalExists($interval);

        $activeUntil = new \DateTime();
        switch ($interval) {
            case SubscriptionInterface::MONTHLY:
                $activeUntil->modify('+1 month');
                break;

            case SubscriptionInterface::YEARLY:
                $activeUntil->modify('+1 year');
                break;
        }

        return $activeUntil;
    }

    /**
     * {@inheritdoc}
     */
    public static function checkIntervalExists(string $interval)
    {
        if (false === self::intervalExists($interval)) {
            throw new \InvalidArgumentException(\Safe\sprintf('The time interval "%s" does not exist. Use SubscriptionInterface to get the right options.', $interval));
        }
    }

    /**
     * {@inheritdoc}
     */
    public static function intervalExists(string $interval): bool
    {
        return \in_array($interval, [
            SubscriptionInterface::DAILY,
            SubscriptionInterface::WEEKLY,
            SubscriptionInterface::BIWEEKLY,
            SubscriptionInterface::MONTHLY,
            SubscriptionInterface::YEARLY,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrency(): Currency
    {
        if (null === $this->currency) {
            $this->currency = new Currency('EUR');
        }

        return $this->currency;
    }

    /**
     * {@inheritdoc}
     */
    public function getFeatures(): SubscribedFeaturesCollection
    {
        return $this->features;
    }

    /**
     * {@inheritdoc}
     */
    public function getRenewInterval(): string
    {
        if (null === $this->renewInterval) {
            // By default the plan is monthly
            $this->renewInterval = SubscriptionInterface::MONTHLY;
        }

        return $this->renewInterval;
    }

    /**
     * {@inheritdoc}
     */
    public function getNextRenewAmount(): MoneyInterface
    {
        if (null === $this->nextRenewAmount) {
            $this->nextRenewAmount = new Money([MoneyInterface::BASE_AMOUNT => 0, MoneyInterface::CURRENCY => $this->getCurrency()]);
        }

        return $this->nextRenewAmount;
    }

    /**
     * {@inheritdoc}
     */
    public function getNextRenewOn(): \DateTime
    {
        if (null === $this->nextRenewOn) {
            $this->nextRenewOn = self::calculateActiveUntil($this->getRenewInterval());
        }

        return $this->nextRenewOn;
    }

    public function getSmallestRefreshInterval(): ?string
    {
        return $this->smallestRefreshInterval;
    }

    public function getNextRefreshOn(): ?\DateTime
    {
        return $this->nextRefreshOn;
    }

    /**
     * {@inheritdoc}
     */
    public function getSubscribedOn(): \DateTime
    {
        if (null === $this->subscribedOn) {
            $this->subscribedOn = new \DateTime();
        }

        return $this->subscribedOn;
    }

    /**
     * {@inheritdoc}
     */
    public function has(string $feature): bool
    {
        if (0 >= \count($this->getFeatures())) {
            return false;
        }

        return $this->getFeatures()->containsKey($feature);
    }

    /**
     * {@inheritdoc}
     */
    public function isStillActive(string $feature): bool
    {
        if (false === $this->has($feature)) {
            return false;
        }

        return $this->getFeatures()->get($feature)->isStillActive();
    }

    /**
     * {@inheritdoc}
     */
    public function setCurrency(Currency $currency): SubscriptionInterface
    {
        $this->currency = $currency;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setFeatures(SubscribedFeaturesCollection $features): SubscriptionInterface
    {
        $this->features = $features;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setRenewInterval(string $renewInterval): SubscriptionInterface
    {
        self::intervalExists($renewInterval);

        $this->renewInterval = $renewInterval;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setMonthly(): SubscriptionInterface
    {
        $this->setRenewInterval(SubscriptionInterface::MONTHLY);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setYearly(): SubscriptionInterface
    {
        $this->setRenewInterval(SubscriptionInterface::YEARLY);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setNextRenewAmount(MoneyInterface $amount): SubscriptionInterface
    {
        $this->nextRenewAmount = $amount;

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @param \DateTime|\DateTimeImmutable $nextRenewOn
     */
    public function setNextRenewOn(\DateTimeInterface $nextRenewOn): SubscriptionInterface
    {
        $this->nextRenewOn = $nextRenewOn;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setNextPaymentInOneMonth(): SubscriptionInterface
    {
        $this->nextRenewOn = clone $this->getNextRenewOn()->modify('+1 month');

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setNextPaymentInOneYear(): SubscriptionInterface
    {
        $this->nextRenewOn = clone $this->getNextRenewOn()->modify('+1 year');

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setSmallestRefreshInterval(string $refreshInterval): SubscriptionInterface
    {
        self::intervalExists($refreshInterval);

        $this->smallestRefreshInterval = $refreshInterval;

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @param \DateTime|\DateTimeImmutable $nextRefreshOn
     */
    public function setNextRefreshOn(\DateTimeInterface $nextRefreshOn): SubscriptionInterface
    {
        $this->nextRefreshOn = $nextRefreshOn;

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @param \DateTime|\DateTimeImmutable $subscribedOn
     */
    public function setSubscribedOn(\DateTimeInterface $subscribedOn): SubscriptionInterface
    {
        $this->subscribedOn = $subscribedOn;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function forceFeaturesUpdate()
    {
        $this->features = clone $this->features;
    }

    /**
     * @ORM\PostLoad()
     */
    public function hydrateFeatures()
    {
        $this->features = new SubscribedFeaturesCollection($this->features);
    }

    public function __clone()
    {
        $this->features = clone $this->features;
    }
}
