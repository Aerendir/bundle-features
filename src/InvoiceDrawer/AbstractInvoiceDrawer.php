<?php

/*
 * This file is part of the Serendipity HQ Features Bundle.
 *
 * Copyright (c) Adamo Aerendir Crespi <aerendir@serendipityhq.com>.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SerendipityHQ\Bundle\FeaturesBundle\InvoiceDrawer;

use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Abstract class to create an InvoiceDrawer.
 */
abstract class AbstractInvoiceDrawer implements InvoiceDrawerInterface
{
    /** @var string */
    public $locale;

    /** @var \NumberFormatter */
    private $currencyFormatter;

    /** @var TranslatorInterface $translator */
    private $translator;

    public function __construct(TranslatorInterface $translator, string $locale)
    {
        if (\class_exists(\NumberFormatter::class)) {
            $this->currencyFormatter = new \NumberFormatter($locale, \NumberFormatter::CURRENCY);
        }

        if (null === $this->currencyFormatter) {
            throw new \RuntimeException('You have to install PHP Intl extension or use Symofony Intl Component to be able to manage Invoices.');
        }

        $this->locale     = $locale;
        $this->translator = $translator;
    }

    protected function getCurrencyFormatter(): \NumberFormatter
    {
        return $this->currencyFormatter;
    }

    protected function getTranslator(): TranslatorInterface
    {
        return $this->translator;
    }
}
