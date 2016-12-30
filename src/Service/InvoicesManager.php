<?php

namespace SerendipityHQ\Bundle\FeaturesBundle\Service;

use SerendipityHQ\Bundle\FeaturesBundle\Model\FeatureInterface;
use SerendipityHQ\Bundle\FeaturesBundle\Model\FeaturesCollection;
use SerendipityHQ\Bundle\FeaturesBundle\Model\Invoice;
use SerendipityHQ\Bundle\FeaturesBundle\Model\InvoiceInterface;
use SerendipityHQ\Bundle\FeaturesBundle\Model\InvoiceLine;
use SerendipityHQ\Bundle\FeaturesBundle\Model\SubscriptionInterface;
use SerendipityHQ\Component\ValueObjects\Currency\Currency;
use SerendipityHQ\Component\ValueObjects\Money\Money;
use SerendipityHQ\Component\ValueObjects\Money\MoneyInterface;

/**
 * Manages the Invoices.
 */
class InvoicesManager
{
    /** @var  FeaturesCollection $configuredFeatures */
    private $configuredFeatures;

    /** @var  SubscriptionInterface $subscription */
    private $subscription;

    /**
     * @param array $configuredFeatures
     */
    public function __construct(array $configuredFeatures)
    {
        $this->configuredFeatures = new FeaturesCollection($configuredFeatures);
    }

    /**
     * Returns all the configured features.
     *
     * @return FeaturesCollection
     */
    public function getConfiguredFeatures() : FeaturesCollection
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
     * @return FeaturesManager
     */
    public function setSubscription(SubscriptionInterface $subscription) : self
    {
        $this->subscription = $subscription;

        return $this;
    }

    /**
     * Returns an Invoice Object.
     *
     * @param InvoiceInterface $invoice
     *
     * @return InvoiceInterface
     */
    public function createInvoice(InvoiceInterface $invoice)
    {
        /** @var FeatureInterface $feature */
        foreach ($this->getSubscription()->getFeatures() as $feature) {
            if (false === $feature->isEnabled())
                continue;

            $price = $this->getConfiguredFeatures()->get($feature->getName())->getPrice($invoice->getCurrency(), $this->getSubscription()->getInterval());

            if ($price instanceof MoneyInterface) {
                $invoiceLine = new InvoiceLine();
                $invoiceLine->setAmount($price)->setDescription($feature->getName());
                $invoice->addLine($invoiceLine);
            }
        }

        return $invoice;
    }
}
