<?php

namespace SerendipityHQ\Bundle\FeaturesBundle\InvoiceDrawer;

use Symfony\Component\Translation\Translator;

/**
 * Abstract class to create an InvoiceDrawer.
 */
abstract class AbstractInvoiceDrawer implements InvoiceDrawerInterface
{
    /** @var  \NumberFormatter */
    private $currencyFormatter;

    /** @var Translator $translator */
    private $translator;

    /**
     * @param Translator $translator
     * @param string $locale
     */
    public function setTranslator(Translator $translator, string $locale)
    {
        if (class_exists(\NumberFormatter::class)) {
            $this->currencyFormatter = new \NumberFormatter($locale, \NumberFormatter::CURRENCY);
        }

        if (null === $this->currencyFormatter) {
            throw new \RuntimeException('You have to install PHP Intl extension or use Symofony Intl Component to be able to manage Invoices.');
        }

        $this->locale     = $locale;
        $this->translator = $translator;
    }

    /**
     * @return \NumberFormatter
     */
    protected function getCurrencyFormatter() : \NumberFormatter
    {
        return $this->currencyFormatter;
    }

    /**
     * @return Translator
     */
    protected function getTranslator() : Translator
    {
        return $this->translator;
    }
}
