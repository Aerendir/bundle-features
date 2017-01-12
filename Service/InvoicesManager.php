<?php

namespace SerendipityHQ\Bundle\FeaturesBundle\Service;

use SerendipityHQ\Bundle\FeaturesBundle\Model\FeatureInterface;
use SerendipityHQ\Bundle\FeaturesBundle\Model\ConfiguredFeaturesCollection;
use SerendipityHQ\Bundle\FeaturesBundle\Model\InvoiceInterface;
use SerendipityHQ\Bundle\FeaturesBundle\Model\InvoiceLine;
use SerendipityHQ\Bundle\FeaturesBundle\Model\SubscribedBooleanFeatureInterface;
use SerendipityHQ\Bundle\FeaturesBundle\Model\SubscriptionInterface;
use SerendipityHQ\Component\ValueObjects\Money\MoneyInterface;

/**
 * Manages the Invoices.
 */
class InvoicesManager
{
    /** @var ConfiguredFeaturesCollection $configuredFeatures */
    private $configuredFeatures;

    /** @var SubscriptionInterface $subscription */
    private $subscription;

    /**
     * @param array $configuredFeatures
     */
    public function __construct(array $configuredFeatures)
    {
        $this->configuredFeatures = new ConfiguredFeaturesCollection($configuredFeatures);
    }

    /**
     * Returns all the configured features.
     *
     * @return ConfiguredFeaturesCollection
     */
    public function getConfiguredFeatures() : ConfiguredFeaturesCollection
    {
        return $this->configuredFeatures;
    }

    /**
     * @return SubscriptionInterface
     */
    public function getSubscription() : SubscriptionInterface
    {
        return $this->subscription;
    }

    /**
     * @param SubscriptionInterface $subscription
     *
     * @return InvoicesManager
     */
    public function setSubscription(SubscriptionInterface $subscription) : self
    {
        $this->subscription = $subscription;

        return $this;
    }

    /**
     * Returns an Invoice Object.
     *
     * If the second argument $addedFeatures is passed, the invoice is populated only with new features added.
     *
     * @param InvoiceInterface $invoice
     * @param array            $addedFeatures
     *
     * @return InvoiceInterface
     */
    public function populateInvoice(InvoiceInterface $invoice, array $addedFeatures = null)
    {
        /** @var SubscribedBooleanFeatureInterface $feature */
        foreach ($this->getSubscription()->getFeatures() as $feature) {
            if ($feature instanceof SubscribedBooleanFeatureInterface && false === $feature->isEnabled()) {
                continue;
            }

            /*
             * If $addedFeatures is passed we have to create an invoice for the new features only.
             *
             * So, if the current processing feature is not in the $addedFeatures array, we don't have to include it in
             * the new Invoice.
             */
            if (null !== $addedFeatures && false === in_array($feature->getName(), $addedFeatures)) {
                continue;
            }

            $price = $this->getConfiguredFeatures()->get($feature->getName())->getPrice($invoice->getCurrency(), $this->getSubscription()->getInterval());

            if ($price instanceof MoneyInterface) {
                $invoiceLine = new InvoiceLine();
                $invoiceLine->setAmount($price)->setDescription($feature->getName());
                $invoice->addLine($invoiceLine, $feature->getName());
            }
        }

        return $invoice;
    }
}
