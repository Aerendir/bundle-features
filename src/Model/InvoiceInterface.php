<?php

namespace SerendipityHQ\Bundle\FeaturesBundle\Model;

use Doctrine\ORM\Mapping as ORM;
use SerendipityHQ\Component\ValueObjects\Currency\CurrencyInterface;
use SerendipityHQ\Component\ValueObjects\Money\MoneyInterface;

/**
 * Interface of an Invoice object.
 */
interface InvoiceInterface
{
    /**
     * @param CurrencyInterface|string $currency
     */
    public function __construct($currency);

    /**
     * @param InvoiceLine $line
     *
     * @return InvoiceInterface
     */
    public function addLine(InvoiceLine $line) : InvoiceInterface;

    /**
     * @return CurrencyInterface
     */
    public function getCurrency() : CurrencyInterface;

    /**
     * @return \DateTime
     */
    public function getIssuedOn() : \DateTime;

    /**
     * @return array
     */
    public function getLines() : array;

    /**
     * @return MoneyInterface
     */
    public function getTotal() : MoneyInterface;
}
