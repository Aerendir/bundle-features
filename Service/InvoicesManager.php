<?php

namespace SerendipityHQ\Bundle\FeaturesBundle\Service;

use SerendipityHQ\Bundle\FeaturesBundle\InvoiceDrawer\InvoiceDrawerInterface;
use SerendipityHQ\Bundle\FeaturesBundle\Model\ConfiguredCountableFeatureInterface;
use SerendipityHQ\Bundle\FeaturesBundle\Model\ConfiguredRechargeableFeatureInterface;
use SerendipityHQ\Bundle\FeaturesBundle\Model\ConfiguredFeaturesCollection;
use SerendipityHQ\Bundle\FeaturesBundle\Model\InvoiceInterface;
use SerendipityHQ\Bundle\FeaturesBundle\Model\InvoiceLine;
use SerendipityHQ\Bundle\FeaturesBundle\Model\InvoiceSection;
use SerendipityHQ\Bundle\FeaturesBundle\Model\SubscribedBooleanFeature;
use SerendipityHQ\Bundle\FeaturesBundle\Model\SubscribedBooleanFeatureInterface;
use SerendipityHQ\Bundle\FeaturesBundle\Model\SubscribedCountableFeature;
use SerendipityHQ\Bundle\FeaturesBundle\Model\SubscribedCountableFeatureInterface;
use SerendipityHQ\Bundle\FeaturesBundle\Model\SubscribedRechargeableFeature;
use SerendipityHQ\Bundle\FeaturesBundle\Model\SubscribedRechargeableFeatureInterface;
use SerendipityHQ\Bundle\FeaturesBundle\Model\SubscriptionInterface;
use SerendipityHQ\Bundle\FeaturesBundle\Property\IsRecurringFeatureInterface;
use SerendipityHQ\Component\ValueObjects\Money\MoneyInterface;
use SHQ\Component\ArrayWriter\ArrayWriter;
use Symfony\Bundle\FrameworkBundle\Translation\Translator;
use Symfony\Component\Form\Test\FormInterface;

/**
 * Manages the Invoices.
 */
class InvoicesManager
{
    /** @var ArrayWriter $arrayWriter */
    private $arrayWriter;

    /** @var ConfiguredFeaturesCollection $configuredFeatures */
    private $configuredFeatures;

    /** @var  string|null $defaultDrawer */
    private $defaultDrawer;

    /** @var  InvoiceDrawerInterface[] $drawers */
    private $drawers;

    /** @var  string $locale */
    private $locale;

    /** @var SubscriptionInterface $subscription */
    private $subscription;


    /**
     * @param array $configuredFeatures
     * @param ArrayWriter $arrayWriter
     * @param string|null $defaultFormatter
     */
    public function __construct(array $configuredFeatures, ArrayWriter $arrayWriter, string $defaultFormatter = null)
    {
        $this->arrayWriter = $arrayWriter;
        $this->configuredFeatures = new ConfiguredFeaturesCollection($configuredFeatures);
        $this->defaultDrawer = $defaultFormatter;
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
     * @param string $name
     * @param InvoiceDrawerInterface $drawer
     */
    public function addDrawer(string $name, InvoiceDrawerInterface $drawer)
    {
        // If this is the default drawer
        if ($this->defaultDrawer === $name) {
            $this->defaultDrawer = $drawer;
        }

        $this->drawers[$name] = $drawer;
    }

    /**
     * @param InvoiceInterface $invoice
     * @param string|null $drawer
     * @return mixed
     */
    public function drawInvoice(InvoiceInterface $invoice, string $drawer = null)
    {
        return $this->getDrawer($drawer)->draw($invoice);
    }

    /**
     * @param string|null $drawer
     * @return InvoiceDrawerInterface
     */
    public function getDrawer(string $drawer = null) : InvoiceDrawerInterface
    {
        // If a Drawer were passed and it exists
        if (null !== $drawer && in_array($drawer, $this->drawers)) {
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
     *
     * @param InvoiceInterface $invoice
     * @param array            $addedFeatures
     *
     * @return InvoiceInterface
     */
    public function populateInvoice(InvoiceInterface $invoice, array $addedFeatures = null)
    {
        $this->populateSection($invoice->getSection('_default'), $addedFeatures);

        return $invoice;
    }

    /**
     * @param InvoiceSection $section
     * @param array|null $addedFeatures
     */
    public function populateSection(InvoiceSection $section, $addedFeatures = null)
    {
        /** @var SubscribedBooleanFeatureInterface $feature */
        foreach ($this->getSubscription()->getFeatures() as $feature) {
            // If this is a BooleanFeature, then we check if it is currently enabled and if...
            if ($feature instanceof SubscribedBooleanFeatureInterface && false === $feature->isEnabled()) {
                // ... it isn't enabled, we have for sure exclude it from the Invoice
                continue;
            }

            // If this is a recurring feature, then we check if it is currently active and if...
            if ($feature instanceof IsRecurringFeatureInterface && false === $feature->isStillActive()) {
                // ... it isn't active, we have for sure exclude it from the Invoice
                continue;
            }

            // If $addedFeatures is passed we have to create an invoice for the new features only, so...
            if (null !== $addedFeatures && false === in_array($feature->getName(), $addedFeatures) && false === $this->arrayWriter->keyExistsNested($addedFeatures, $feature->getName())) {
                // ... if the current processing feature is not in the $addedFeatures array, we don't have to include it in the new Invoice.
                continue;
            }

            $price = null;
            // The feature has to be added
            switch (get_class($feature)) {
                case SubscribedBooleanFeature::class:
                    // The price is recurrent, so we need to pass the subscription interval
                    $price = $this->getConfiguredFeatures()->get($feature->getName())->getPrice($this->getSubscription()->getCurrency(), $this->getSubscription()->getInterval());
                    break;
                case SubscribedCountableFeature::class:
                    /**
                     * @var ConfiguredCountableFeatureInterface $configuredFeature
                     * @var SubscribedCountableFeatureInterface $feature
                     */
                    $configuredFeature = $this->getConfiguredFeatures()->get($feature->getName());

                    // The price is recurrent, so we need to pass the subscription interval // @todo For the moment force the use of packs' prices
                    $price = $configuredFeature->getPack($feature->getSubscribedPack()->getNumOfUnits())->getPrice($this->getSubscription()->getCurrency(), $this->getSubscription()->getInterval());
                    $quantity = $feature->getSubscribedPack()->getNumOfUnits();
                    break;
                case SubscribedRechargeableFeature::class:
                    /**
                     * @var ConfiguredRechargeableFeatureInterface $configuredFeature
                     * @var SubscribedRechargeableFeatureInterface $feature
                     */
                    $configuredFeature = $this->getConfiguredFeatures()->get($feature->getName());

                    // The price is unatantum, so we don't need to pass the subscription interval // @todo For the moment force the use of packs' prices
                    $price = $configuredFeature->getPack($feature->getRechargingPack()->getNumOfUnits())->getPrice($this->getSubscription()->getCurrency());
                    $quantity = $feature->getRechargingPack()->getNumOfUnits();
                    break;
            }

            if ($price instanceof MoneyInterface) {
                $invoiceLine = new InvoiceLine();
                $invoiceLine->setAmount($price)->setDescription($feature->getName())->setQuantity($quantity ?? null);
                $section->addLine($invoiceLine, $feature->getName());
            }
        }
    }
}
