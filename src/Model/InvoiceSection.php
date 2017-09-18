<?php

/*
 * This file is part of the SHQFeaturesBundle.
 *
 * Copyright Adamo Aerendir Crespi 2016-2017.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    Adamo Aerendir Crespi <hello@aerendir.me>
 * @copyright Copyright (C) 2016 - 2017 Aerendir. All rights reserved.
 * @license   MIT License.
 */

namespace SerendipityHQ\Bundle\FeaturesBundle\Model;

use \Money\Currency;
use SerendipityHQ\Component\ValueObjects\Money\Money;
use SerendipityHQ\Component\ValueObjects\Money\MoneyInterface;

/**
 * Represents a section into the Invoice.
 */
class InvoiceSection implements \JsonSerializable
{
    /** @var Currency $currency */
    private $currency;

    /** @var InvoiceSectionHeader */
    private $header;

    /** @var InvoiceLine[] */
    private $lines = [];

    /** @var MoneyInterface $grossTotal */
    private $grossTotal;

    /** @var MoneyInterface $netTotal */
    private $netTotal;

    /**
     * @param Currency $currency
     */
    public function __construct(Currency $currency)
    {
        $this->currency   = $currency;
        $this->grossTotal = new Money(['amount' => 0, 'currency' => $currency]);
        $this->netTotal   = new Money(['amount' => 0, 'currency' => $currency]);
    }

    /**
     * @return Currency
     */
    public function getCurrency(): Currency
    {
        return $this->currency;
    }

    /**
     * @return InvoiceSectionHeader
     */
    public function getHeader()
    {
        return $this->header;
    }

    /**
     * @return bool
     */
    public function hasHeader(): bool
    {
        return isset($this->header);
    }

    /**
     * @return InvoiceSection
     */
    public function removeHeader(): InvoiceSection
    {
        $this->header = null;

        return $this;
    }

    /**
     * @param InvoiceSectionHeader $header
     *
     * @return InvoiceSection
     */
    public function setHeader(InvoiceSectionHeader $header): InvoiceSection
    {
        $this->header = $header;

        return $this;
    }

    /**
     * @param InvoiceLine $line
     * @param string|null $id
     *
     * @return InvoiceSection
     */
    public function addLine(InvoiceLine $line, string $id = null): InvoiceSection
    {
        switch (gettype($id)) {
            case 'string':
            case 'integer':
                if ($this->hasLine($id)) {
                    throw new \LogicException(sprintf('The section "%s" already exists. You cannot add it again', $id));
                }

                $this->lines[$id] = $line;
                break;
            case 'NULL':
                $this->lines[] = $line;
                break;
            default:
                throw new \InvalidArgumentException(sprintf('Invalid $id type. Accepted types are "string, "integer" and "null". You passed "%s".', gettype($id)));
        }

        // Set the new Total
        $this->grossTotal = $this->getGrossTotal()->add($line->getGrossAmount());
        $this->netTotal   = $this->getNetTotal()->add($line->getNetAmount());

        return $this;
    }

    /**
     * @param int|string $id
     *
     * @return InvoiceLine|null
     */
    public function getLine($id)
    {
        return $this->lines[$id] ?? null;
    }

    /**
     * @return array
     */
    public function getLines(): array
    {
        return $this->lines;
    }

    /**
     * @param int|string $id
     *
     * @return bool
     */
    public function hasLine($id)
    {
        if (false === is_string($id) && false === is_int($id)) {
            throw new \InvalidArgumentException(sprintf('Only strings or integers are accepted as $id. "%s" passed.', gettype($id)));
        }

        return isset($this->lines[$id]);
    }

    /**
     * @param string| int $id
     *
     * @return bool|InvoiceLine The removed InvoiceLine or false if it isn't found
     */
    public function removeLine($id)
    {
        if ($this->hasLine($id)) {
            $return = $this->lines[$id];
            unset($this->lines[$id]);

            $this->recalculateTotal();

            return $return;
        }

        return false;
    }

    /**
     * @return MoneyInterface
     */
    public function getGrossTotal(): MoneyInterface
    {
        return $this->grossTotal;
    }

    /**
     * @return MoneyInterface
     */
    public function getNetTotal(): MoneyInterface
    {
        return $this->netTotal;
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize()
    {
        $return = [];
        if (null !== $this->getHeader()) {
            $return['_header'] = $this->getHeader()->getHeader();
        }

        $lines = [];
        foreach ($this->getLines() as $lineId => $line) {
            $lines[$lineId] = $line;
        }

        $return['_lines'] = $lines;

        return $return;
    }

    /**
     * Recalculates the total of the invoice.
     */
    private function recalculateTotal()
    {
        $this->grossTotal = new Money(['amount' => 0, 'currency' => $this->getCurrency()]);
        $this->netTotal   = new Money(['amount' => 0, 'currency' => $this->getCurrency()]);

        /** @var InvoiceLine $line */
        foreach ($this->getLines() as $line) {
            $this->grossTotal = $this->grossTotal->add($line->getGrossAmount());
            $this->netTotal   = $this->netTotal->add($line->getNetAmount());
        }
    }
}
