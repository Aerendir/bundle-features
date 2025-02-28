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

namespace SerendipityHQ\Bundle\FeaturesBundle\Model;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Money\Currency;
use SerendipityHQ\Bundle\FeaturesBundle\Model\Feature\FeatureInterface;
use SerendipityHQ\Bundle\FeaturesBundle\Model\Feature\Subscribed\SubscribedFeaturesCollection;
use SerendipityHQ\Component\ValueObjects\Money\Money;
use SerendipityHQ\Component\ValueObjects\Money\MoneyInterface;

use function Safe\sprintf;

/**
 * Basic properties and methods to manage a subscription.
 *
 */
#[ORM\MappedSuperclass]
#[ORM\HasLifecycleCallbacks]
abstract class Subscription implements SubscriptionInterface
{

    #[ORM\Column( type: 'currency', nullable: true)]
    private ?Currency $currency = null;

    /**
     * Contains the $featuresArray as a FeatureCollection.
     *
     */
    #[ORM\Column( type: Types::JSON, nullable: true)]
    private array|SubscribedFeaturesCollection|null $features = null;

    #[ORM\Column( type: Types::STRING, nullable: true)]
    private ?string $renewInterval = null;

    #[ORM\Column( type: 'money', nullable: true)]
    private ?MoneyInterface $nextRenewAmount = null;

    #[ORM\Column( type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $nextRenewOn = null;

    /**
     * If there are countable features, this field saves the smallest refresh interval found.
     *
     */
    #[ORM\Column( type: Types::STRING, nullable: true)]
    private string $smallestRefreshInterval;

    /**
     * If there are countable features configured, this field is used to determine when they have to be refresh based on
     * the smallest interval.
     *
     */
    #[ORM\Column( type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $nextRefreshOn = null;


    #[ORM\Column( type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $subscribedOn = null;

    public function __clone()
    {
        $this->features = clone $this->features;
    }

    public function addFeature(string $featureName, FeatureInterface $feature): SubscriptionInterface
    {
        $this->getFeatures()->set($featureName, $feature);

        return $this;
    }

    public static function calculateActiveUntil(string $interval): \DateTimeInterface
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

    public static function checkIntervalExists(string $interval): void
    {
        if (false === self::intervalExists($interval)) {
            throw new \InvalidArgumentException(sprintf('The time interval "%s" does not exist. Use SubscriptionInterface to get the right options.', $interval));
        }
    }

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

    public function getCurrency(): Currency
    {
        if (null === $this->currency) {
            $this->currency = new Currency('EUR');
        }

        return $this->currency;
    }

    public function getFeatures(): SubscribedFeaturesCollection
    {
        return $this->features;
    }

    public function getRenewInterval(): string
    {
        if (null === $this->renewInterval) {
            // By default the plan is monthly
            $this->renewInterval = SubscriptionInterface::MONTHLY;
        }

        return $this->renewInterval;
    }

    public function getNextRenewAmount(): MoneyInterface
    {
        if (null === $this->nextRenewAmount) {
            $this->nextRenewAmount = new Money([MoneyInterface::BASE_AMOUNT => 0, MoneyInterface::CURRENCY => $this->getCurrency()]);
        }

        return $this->nextRenewAmount;
    }

    /**
     * @return \DateTime|\DateTimeImmutable
     */
    public function getNextRenewOn(): \DateTimeInterface
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

    /**
     * @return \DateTime|\DateTimeImmutable|null
     */
    public function getNextRefreshOn(): ?\DateTimeInterface
    {
        return $this->nextRefreshOn;
    }

    public function getSubscribedOn(): \DateTimeInterface
    {
        if (null === $this->subscribedOn) {
            $this->subscribedOn = new \DateTime();
        }

        return $this->subscribedOn;
    }

    public function has(string $feature): bool
    {
        if (0 >= \count($this->getFeatures())) {
            return false;
        }

        return $this->getFeatures()->containsKey($feature);
    }

    public function isStillActive(string $feature): bool
    {
        if (false === $this->has($feature)) {
            return false;
        }

        return $this->getFeatures()->get($feature)->isStillActive();
    }

    public function setCurrency(Currency $currency): SubscriptionInterface
    {
        $this->currency = $currency;

        return $this;
    }

    public function setFeatures(SubscribedFeaturesCollection $features): SubscriptionInterface
    {
        $this->features = $features;

        return $this;
    }

    public function setRenewInterval(string $renewInterval): SubscriptionInterface
    {
        self::intervalExists($renewInterval);

        $this->renewInterval = $renewInterval;

        return $this;
    }

    public function setMonthly(): SubscriptionInterface
    {
        $this->setRenewInterval(SubscriptionInterface::MONTHLY);

        return $this;
    }

    public function setYearly(): SubscriptionInterface
    {
        $this->setRenewInterval(SubscriptionInterface::YEARLY);

        return $this;
    }

    public function setNextRenewAmount(MoneyInterface $amount): SubscriptionInterface
    {
        $this->nextRenewAmount = $amount;

        return $this;
    }

    /**
     * @param \DateTime|\DateTimeImmutable $nextRenewOn
     */
    public function setNextRenewOn(\DateTimeInterface $nextRenewOn): SubscriptionInterface
    {
        $this->nextRenewOn = $nextRenewOn;

        return $this;
    }

    public function setNextPaymentInOneMonth(): SubscriptionInterface
    {
        $this->nextRenewOn = clone $this->getNextRenewOn()->modify('+1 month');

        return $this;
    }

    public function setNextPaymentInOneYear(): SubscriptionInterface
    {
        $this->nextRenewOn = clone $this->getNextRenewOn()->modify('+1 year');

        return $this;
    }

    public function setSmallestRefreshInterval(string $refreshInterval): SubscriptionInterface
    {
        self::intervalExists($refreshInterval);

        $this->smallestRefreshInterval = $refreshInterval;

        return $this;
    }

    /**
     * @param \DateTime|\DateTimeImmutable $nextRefreshOn
     */
    public function setNextRefreshOn(\DateTimeInterface $nextRefreshOn): SubscriptionInterface
    {
        $this->nextRefreshOn = $nextRefreshOn;

        return $this;
    }

    /**
     * @param \DateTime|\DateTimeImmutable $subscribedOn
     */
    public function setSubscribedOn(\DateTimeInterface $subscribedOn): SubscriptionInterface
    {
        $this->subscribedOn = $subscribedOn;

        return $this;
    }

    public function forceFeaturesUpdate(): void
    {
        $this->features = clone $this->features;
    }

    #[ORM\PostLoad]
    public function hydrateFeatures(): void
    {
        $this->features = new SubscribedFeaturesCollection($this->features);
    }
}
