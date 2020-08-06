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

use Money\Currency;
use SerendipityHQ\Component\ValueObjects\Money\MoneyInterface;

/**
 * Interface of an Invoice object.
 */
interface InvoiceInterface extends \JsonSerializable
{
    /**
     * @param Currency|string $currency
     */
    public function __construct($currency);

    public function getHeader(): ?\SerendipityHQ\Bundle\FeaturesBundle\Model\InvoiceSectionHeader;

    public function hasHeader(): bool;

    /**
     * @return bool|InvoiceSectionHeader
     */
    public function removeHeader();

    /**
     * @param InvoiceSectionHeader $header
     */
    public function setHeader(InvoiceSectionHeader $header);

    /**
     * Adds an Invoice line to the _default section of this invoice.
     *
     * @param InvoiceLine $line
     * @param string      $id   The ID of the line to make it identifiable so it can be retrieved with the getLine method
     *
     * @return InvoiceInterface
     */
    public function addLine(InvoiceLine $line, string $id = null): InvoiceInterface;

    /**
     * Returns a specific line of the _default section of the Invoice.
     *
     * @param string!int $id
     */
    public function getLine($id): \SerendipityHQ\Bundle\FeaturesBundle\Model\InvoiceLine;

    /**
     * @return array
     */
    public function getLines(): array;

    /**
     * @param int|string $id
     */
    public function hasLine($id): bool;

    /**
     * @param string| int $id
     *
     * @return bool|InvoiceLine The removed InvoiceLine or false if it isn't found
     */
    public function removeLine($id);

    /**
     * @param InvoiceSection $section
     * @param string|null    $id
     */
    public function addSection(InvoiceSection $section, string $id = null): \SerendipityHQ\Bundle\FeaturesBundle\Model\InvoiceInterface;

    /**
     * @param int|string $id
     */
    public function getSection($id):? \SerendipityHQ\Bundle\FeaturesBundle\Model\InvoiceSection;

    /**
     * Get the sections of the Invoice.
     *
     * @return array
     */
    public function getSections(): array;

    /**
     * @param int|string $id
     *
     * @return bool
     */
    public function hasSection($id): bool;

    /**
     * @param int|string $id
     *
     * @return bool|InvoiceInterface
     */
    public function removeSection($id);

    /**
     * @return Currency
     */
    public function getCurrency(): Currency;

    /**
     * @return \DateTime
     */
    public function getIssuedOn(): \DateTime;

    /**
     * @return MoneyInterface
     */
    public function getGrossTotal(): MoneyInterface;

    /**
     * @return MoneyInterface
     */
    public function getNetTotal(): MoneyInterface;

    /**
     * Generates the number of the Invoice.
     *
     * The concrete implementation of this method is left to the iplementer as there are so many cases that is
     * impossible to abstract them and generalize the process.
     *
     * @return int|string
     */
    public function generateNumber();

    /**
     * Rehydrate the object when loaded from the database.
     */
    public function jsonUnserialize();
}
