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

namespace SerendipityHQ\Bundle\FeaturesBundle\Manager;

use SerendipityHQ\Bundle\FeaturesBundle\InvoiceDrawer\InvoiceDrawerInterface;
use SerendipityHQ\Bundle\FeaturesBundle\Model\Feature\Configured\ConfiguredFeaturesCollection;
use SerendipityHQ\Bundle\FeaturesBundle\Model\Feature\FeatureInterface;
use SerendipityHQ\Bundle\FeaturesBundle\Model\Feature\Property\IsRecurringFeatureInterface;
use SerendipityHQ\Bundle\FeaturesBundle\Model\Feature\Subscribed\SubscribedBooleanFeature;
use SerendipityHQ\Bundle\FeaturesBundle\Model\Feature\Subscribed\SubscribedCountableFeature;
use SerendipityHQ\Bundle\FeaturesBundle\Model\Feature\Subscribed\SubscribedRechargeableFeature;
use SerendipityHQ\Bundle\FeaturesBundle\Model\InvoiceInterface;
use SerendipityHQ\Bundle\FeaturesBundle\Model\InvoiceLine;
use SerendipityHQ\Bundle\FeaturesBundle\Model\InvoiceSection;
use SerendipityHQ\Bundle\FeaturesBundle\Model\SubscriptionInterface;
use SerendipityHQ\Component\ArrayWriter\ArrayWriter;
use SerendipityHQ\Component\ValueObjects\Money\MoneyInterface;

/**
 * Manages the Invoices.
 */
final class InvoicesManager
{
    private ArrayWriter $arrayWriter;
    private ConfiguredFeaturesCollection $configuredFeatures;
    private ?string $defaultDrawer;

    /** @var InvoiceDrawerInterface[] $drawers */
    private array $drawers;

    private SubscriptionInterface $subscription;

    /**
     * @param array<string, InvoiceDrawerInterface> $drawers
     */
    public function __construct(array $configuredFeatures, ArrayWriter $arrayWriter, string $defaultFormatter = null, array $drawers = [])
    {
        $this->arrayWriter        = $arrayWriter;
        $this->defaultDrawer      = $defaultFormatter;
        $this->configuredFeatures = new ConfiguredFeaturesCollection($configuredFeatures);

        foreach ($drawers as $drawerName => $drawer) {
            $this->addDrawer($drawerName, $drawer);
        }
    }

    /**
     * Returns all the configured features.
     */
    public function getConfiguredFeatures(): ConfiguredFeaturesCollection
    {
        return $this->configuredFeatures;
    }

    public function getSubscription(): SubscriptionInterface
    {
        return $this->subscription;
    }

    public function setSubscription(SubscriptionInterface $subscription): self
    {
        $this->subscription = $subscription;

        return $this;
    }

    public function addDrawer(string $name, InvoiceDrawerInterface $drawer): self
    {
        // If this is the default drawer
        if ($this->defaultDrawer === $name) {
            $this->defaultDrawer = $drawer;
        }

        $this->drawers[$name] = $drawer;

        return $this;
    }

    public function drawInvoice(InvoiceInterface $invoice, string $drawer = null): array
    {
        return $this->getDrawer($drawer)->draw($invoice);
    }

    public function getDrawer(string $drawer = null): InvoiceDrawerInterface
    {
        // If a Drawer were passed and it exists
        if (null !== $drawer && \in_array($drawer, $this->drawers)) {
            // Use it
            $drawer = $this->drawers[$drawer];
        }

        // If a drawer were not passed...
        if (null === $drawer) {
            // ... check for the existence of a default one and it doesn't exist...
            if (null === $this->defaultDrawer) {
                // ... Throw an error
                throw new \LogicException('To draw an Invoice you have to pass an InvoiceDrawerInterface drawer or either set a default drawer in the features set.');
            }

            $drawer = $this->defaultDrawer;
        }

        return $drawer;
    }

    /**
     * Returns an Invoice Object.
     *
     * If the second argument $addedFeatures is passed, the invoice is populated only with new features added.
     * If it is not passed, the invoice is populated with the current Subscription and takes into account only the
     * IsRecurringFeature(s).
     * This is useful to show the user his next invoice amount.
     */
    public function populateInvoice(InvoiceInterface $invoice, array $addedFeatures = null): InvoiceInterface
    {
        $this->populateSection($invoice->getSection('_default'), $addedFeatures);

        return $invoice;
    }

    /**
     * This method is used to populate an Invoice Section.
     *
     * An InvoiceSection is populared because the Subscription is updated with new features ($addedFeatures) or because
     * the Subscription is being renew.
     *
     * The two cases MUST be keep distinguished as in the first we have to add to the InvoiceSection only the newly
     * added features; in the second, instead, we have to add all the subscribed features.
     */
    public function populateSection(InvoiceSection $section, array $addedFeatures = null): void
    {
        /** @var SubscribedBooleanFeature $feature */
        foreach ($this->buildPopulatingFeatures($addedFeatures) as $feature) {
            $grossPrice = null;
            $netPrice   = null;
            // The feature has to be added
            switch (\get_class($feature)) {
                case SubscribedBooleanFeature::class:
                    /**
                     * The price is recurrent, so we need to pass the subscription interval.
                     */
                    $grossPrice = $this->getConfiguredFeatures()->get($feature->getName())->getPrice($this->getSubscription()->getCurrency(), $this->getSubscription()->getRenewInterval(), FeatureInterface::PRICE_GROSS);
                    $netPrice   = $this->getConfiguredFeatures()->get($feature->getName())->getPrice($this->getSubscription()->getCurrency(), $this->getSubscription()->getRenewInterval(), FeatureInterface::PRICE_NET);

                    break;
                case SubscribedCountableFeature::class:
                    $configuredFeature = $this->getConfiguredFeatures()->get($feature->getName());

                    // The price is recurrent, so we need to pass the subscription interval // @todo For the moment force the use of packs' prices
                    $grossPrice = $configuredFeature->getPack($feature->getSubscribedPack()->getNumOfUnits())->getPrice($this->getSubscription()->getCurrency(), $this->getSubscription()->getRenewInterval(), FeatureInterface::PRICE_GROSS);
                    $netPrice   = $configuredFeature->getPack($feature->getSubscribedPack()->getNumOfUnits())->getPrice($this->getSubscription()->getCurrency(), $this->getSubscription()->getRenewInterval(), FeatureInterface::PRICE_NET);
                    $quantity   = $feature->getSubscribedPack()->getNumOfUnits();

                    break;
                case SubscribedRechargeableFeature::class:
                    $configuredFeature = $this->getConfiguredFeatures()->get($feature->getName());

                    // The price is unatantum, so we don't need to pass the subscription interval // @todo For the moment force the use of packs' prices
                    $grossPrice = $configuredFeature->getPack($feature->getRechargingPack()->getNumOfUnits())->getPrice($this->getSubscription()->getCurrency(), FeatureInterface::PRICE_GROSS);
                    $netPrice   = $configuredFeature->getPack($feature->getRechargingPack()->getNumOfUnits())->getPrice($this->getSubscription()->getCurrency(), FeatureInterface::PRICE_NET);
                    $quantity   = $feature->getRechargingPack()->getNumOfUnits();

                    break;
            }

            if ($grossPrice instanceof MoneyInterface) {
                $invoiceLine = new InvoiceLine();
                $invoiceLine
                    ->setGrossAmount($grossPrice)
                    ->setNetAmount($netPrice)
                    ->setDescription($feature->getName())
                    ->setQuantity($quantity ?? null)
                    ->setTaxName($feature->getConfiguredFeature()->getTaxName())
                    ->setTaxRate($feature->getConfiguredFeature()->getTaxRate());
                $section->addLine($invoiceLine, $feature->getName());
            }
        }
    }

    /**
     * Decides if the features to add to the InvoiceSection are the ones added or the ones already present in the
     * Subscription.
     *
     * Then builds the array to process to add the features to
     */
    private function buildPopulatingFeatures(?array $addedFeatures): array
    {
        $populatingFeatures = [];

        // If we $added features is not null, then we have to add only them to the Invoice section as the invoice is
        // being built for an update of the Subscription
        if (null !== $addedFeatures) {
            /**
             * $feature is an array if the it is a Rechargeable one.
             *
             * These features are in the form [0 => [feature_name => 10]], as they have to tell the feature name and amount
             * bought while the other kind of features (Boolean and Countable) only tell the name of the feature and so
             * are in the form [0 => 'feature_name'].
             */
            foreach ($addedFeatures as $feature) {
                // If $feature is a Rechargeable one, we have to extract its name (that is the key of the deeper array)
                if (\is_array($feature)) {
                    $feature = \key($feature);
                }

                // We now get the Subscribed*Feature object directly from the Subscription as it is already updated at this point
                $populatingFeatures[] = $this->getSubscription()->getFeatures()->get($feature);
            }

            return $populatingFeatures;
        }

        // If the $addedFeatures array is null, instead, we have to build the invoice for ALL the already subscribed features,
        // so we return the entire array of subscribed features, but skipping the Rechargeable ones as they are not renewable

        foreach ($this->getSubscription()->getFeatures() as $feature) {
            if (
                // If is a still enabled BooleanFeature
                ($feature instanceof SubscribedBooleanFeature && $feature->isEnabled())
                // OR is a still active RecurringFeature
                || ($feature instanceof IsRecurringFeatureInterface && $feature->isStillActive())
            ) {
                // We add it to the returning array
                $populatingFeatures[] = $feature;
            }
        }

        return $populatingFeatures;
    }
}
