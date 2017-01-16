<?php

namespace SerendipityHQ\Bundle\FeaturesBundle\Model;

use SerendipityHQ\Component\ValueObjects\Currency\CurrencyInterface;
use SerendipityHQ\Component\ValueObjects\Money\MoneyInterface;

/**
 * Interface of an Invoice object.
 */
interface InvoiceInterface extends \JsonSerializable
{
    /**
     * @param CurrencyInterface|string $currency
     */
    public function __construct($currency);

    /**
     * @return null|InvoiceSectionHeader
     */
    public function getHeader();

    /**
     * @return bool
     */
    public function hasHeader();

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
    public function addLine(InvoiceLine $line, string $id = null) : InvoiceInterface;

    /**
     * Returns a specific line of the _default section of the Invoice.
     *
     * @param string!int $id
     *
     * @return InvoiceLine
     */
    public function getLine($id);

    /**
     * @return array
     */
    public function getLines() : array;

    /**
     * @param string|int $id
     *
     * @return bool
     */
    public function hasLine($id);

    /**
     * @param string| int $id
     *
     * @return bool|InvoiceLine The removed InvoiceLine or false if it isn't found
     */
    public function removeLine($id);

    /**
     * @param InvoiceSection $section
     * @param string|null      $id
     *
     * @return InvoiceInterface
     */
    public function addSection(InvoiceSection $section, string $id = null);

    /**
     * @param string|int $id
     * @return InvoiceSection
     */
    public function getSection($id);

    /**
     * Get the sections of the Invoice.
     *
     * @return array
     */
    public function getSections() : array;

    /**
     * @param string|int $id
     *
     * @return bool
     */
    public function hasSection($id) : bool;

    /**
     * @param string|int $id
     *
     * @return bool|InvoiceInterface
     */
    public function removeSection($id);

    /**
     * @return CurrencyInterface
     */
    public function getCurrency() : CurrencyInterface;

    /**
     * @return \DateTime
     */
    public function getIssuedOn() : \DateTime;

    /**
     * @return MoneyInterface
     */
    public function getTotal() : MoneyInterface;

    /**
     * Generates the number of the Invoice.
     *
     * The concrete implementation of this method is left to the iplementer as there are so many cases that is
     * impossible to abstract them and generalize the process.
     *
     * @return string|int
     */
    public function generateNumber();

    /**
     * Rehydrate the object when loaded from the database.
     */
    public function jsonUnserialize();
}
