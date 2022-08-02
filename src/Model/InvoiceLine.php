<?php

declare(strict_types=1);

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
 * This entity will never be persisted.
 *
 * The lines are serialized by Issue when persisted to the database and deserialized when get from it.
 */
final class InvoiceLine implements \JsonSerializable
{
    private const FIELD_CURRENCY     = 'currency';
    private const FIELD_GROSS_AMOUNT = 'gross_amount';
    private const FIELD_NET_AMOUNT   = 'net_amount';
    private const FIELD_DESCRIPTION  = 'description';
    private const FIELD_QUANTITY     = 'quantity';
    private const FIELD_TAX_NAME     = 'tax_name';
    private const FIELD_TAX_RATE     = 'tax_rate';

    private MoneyInterface $grossAmount;
    private MoneyInterface $netAmount;
    private string $description;
    private int $quantity;
    private string $taxName;
    private float $taxRate;

    public function __toArray(): array
    {
        return [
            self::FIELD_GROSS_AMOUNT => $this->getGrossAmount()->getBaseAmount(),
            self::FIELD_NET_AMOUNT   => $this->getNetAmount()->getBaseAmount(),
            self::FIELD_CURRENCY     => $this->getGrossAmount()->getCurrency()->getCode(),
            self::FIELD_DESCRIPTION  => $this->getDescription(),
            self::FIELD_QUANTITY     => $this->getQuantity(),
            self::FIELD_TAX_NAME     => $this->getTaxName(),
            self::FIELD_TAX_RATE     => $this->getTaxRate(),
        ];
    }

    public function getGrossAmount(): MoneyInterface
    {
        return $this->grossAmount;
    }

    public function getNetAmount(): MoneyInterface
    {
        return $this->netAmount;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function getTaxName(): string
    {
        return $this->taxName;
    }

    public function getTaxRate(): float
    {
        return $this->taxRate;
    }

    public function setGrossAmount(MoneyInterface $grossAmount): self
    {
        $this->grossAmount = $grossAmount;

        return $this;
    }

    public function setNetAmount(MoneyInterface $netAmount): self
    {
        $this->netAmount = $netAmount;

        return $this;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function setQuantity(?int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function setTaxName(string $taxName): self
    {
        $this->taxName = $taxName;

        return $this;
    }

    public function setTaxRate(float $taxRate): self
    {
        $this->taxRate = $taxRate;

        return $this;
    }

    public function jsonSerialize(): array
    {
        return $this->__toArray();
    }

    public function hydrate(array $data): void
    {
        $grossAmount = new Money([MoneyInterface::BASE_AMOUNT => (int) $data[self::FIELD_GROSS_AMOUNT], MoneyInterface::CURRENCY => new Currency($data[self::FIELD_CURRENCY])]);
        $netAmount   = new Money([MoneyInterface::BASE_AMOUNT => (int) $data[self::FIELD_NET_AMOUNT], MoneyInterface::CURRENCY => new Currency($data[self::FIELD_CURRENCY])]);
        $this->setDescription($data[self::FIELD_DESCRIPTION]);
        $this->setGrossAmount($grossAmount);
        $this->setNetAmount($netAmount);
        $this->setQuantity((int) $data[self::FIELD_QUANTITY]);
        $this->setTaxName($data[self::FIELD_TAX_NAME]);
        $this->setTaxRate($data[self::FIELD_TAX_RATE]);
    }
}
