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
use SerendipityHQ\Component\ValueObjects\Money\Money;
use SerendipityHQ\Component\ValueObjects\Money\MoneyInterface;

/**
 * Represents a section into the Invoice.
 */
final class InvoiceSection implements \JsonSerializable
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
     * @var string
     */
    private const BASE_AMOUNT = 'baseAmount';
    /**
     * @var string
     */
    private const CURRENCY = 'currency';

    /**
     * @param Currency $currency
     */
    public function __construct(Currency $currency)
    {
        $this->currency   = $currency;
        $this->grossTotal = new Money([self::BASE_AMOUNT => 0, self::CURRENCY => $currency]);
        $this->netTotal   = new Money([self::BASE_AMOUNT => 0, self::CURRENCY => $currency]);
    }

    /**
     * @return Currency
     */
    public function getCurrency(): Currency
    {
        return $this->currency;
    }

    public function getHeader(): \SerendipityHQ\Bundle\FeaturesBundle\Model\InvoiceSectionHeader
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

    public function removeHeader(): self
    {
        $this->header = null;

        return $this;
    }

    /**
     * @param InvoiceSectionHeader $header
     */
    public function setHeader(InvoiceSectionHeader $header): self
    {
        $this->header = $header;

        return $this;
    }

    /**
     * @param InvoiceLine $line
     * @param string|null $id
     */
    public function addLine(InvoiceLine $line, string $id = null): self
    {
        switch (\gettype($id)) {
            case 'string':
            case 'integer':
                if ($this->hasLine($id)) {
                    throw new \LogicException(\Safe\sprintf('The section "%s" already exists. You cannot add it again', $id));
                }

                $this->lines[$id] = $line;
                break;
            case 'NULL':
                $this->lines[] = $line;
                break;
            default:
                throw new \InvalidArgumentException(\Safe\sprintf('Invalid $id type. Accepted types are "string, "integer" and "null". You passed "%s".', \gettype($id)));
        }

        // Set the new Total
        $this->grossTotal = $this->getGrossTotal()->add($line->getGrossAmount());
        $this->netTotal   = $this->getNetTotal()->add($line->getNetAmount());

        return $this;
    }

    /**
     * @param int|string $id
     */
    public function getLine($id): ?\SerendipityHQ\Bundle\FeaturesBundle\Model\InvoiceLine
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
     */
    public function hasLine($id): bool
    {
        if (false === \is_string($id) && false === \is_int($id)) {
            throw new \InvalidArgumentException(\Safe\sprintf('Only strings or integers are accepted as $id. "%s" passed.', \gettype($id)));
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
    public function jsonSerialize(): array
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
    private function recalculateTotal(): void
    {
        $this->grossTotal = new Money([self::BASE_AMOUNT => 0, self::CURRENCY => $this->getCurrency()]);
        $this->netTotal   = new Money([self::BASE_AMOUNT => 0, self::CURRENCY => $this->getCurrency()]);

        /** @var InvoiceLine $line */
        foreach ($this->getLines() as $line) {
            $this->grossTotal = $this->grossTotal->add($line->getGrossAmount());
            $this->netTotal   = $this->netTotal->add($line->getNetAmount());
        }
    }
}
