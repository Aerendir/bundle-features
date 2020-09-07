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
 * This entity will never be persisted.
 *
 * The lines are serialized by Issue when persisted to the database and deserialized when get from it.
 */
final class InvoiceLine implements \JsonSerializable
{
    private const CURRENCY = 'currency';
    /** @var MoneyInterface$grossAmount */
    private $grossAmount;

    /** @var MoneyInterface$netAmount */
    private $netAmount;

    /** @var string $description */
    private $description;

    /** @var string $quantity */
    private $quantity;

    /** @var string $taxName */
    private $taxName;

    /** @var float $taxRate */
    private $taxRate;

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

    /**
     * @return int|null
     */
    public function getQuantity(): string
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

    /**
     * @param MoneyInterface$grossAmount
     */
    public function setGrossAmount(MoneyInterface $grossAmount): self
    {
        $this->grossAmount = $grossAmount;

        return $this;
    }

    /**
     * @param MoneyInterface$netAmount
     */
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

    /**
     * @return InvoiceLine
     */
    public function setQuantity(?int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * @return InvoiceLine
     */
    public function setTaxName(string $taxName): self
    {
        $this->taxName = $taxName;

        return $this;
    }

    /**
     * @return InvoiceLine
     */
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
        $grossAmount = new Money([MoneyInterface::BASE_AMOUNT => (int) $data['gross_amount'], MoneyInterface::CURRENCY => new Currency($data[self::CURRENCY])]);
        $netAmount   = new Money([MoneyInterface::BASE_AMOUNT => (int) $data['net_amount'], MoneyInterface::CURRENCY => new Currency($data[self::CURRENCY])]);
        $this->setDescription($data['description']);
        $this->setGrossAmount($grossAmount);
        $this->setNetAmount($netAmount);
        $this->setQuantity((int) $data['quantity']);
        $this->setTaxName($data['tax_name']);
        $this->setTaxRate($data['tax_rate']);
    }

    public function __toArray(): array
    {
        return [
            'gross_amount'     => $this->getGrossAmount()->getBaseAmount(),
            'net_amount'       => $this->getNetAmount()->getBaseAmount(),
            self::CURRENCY     => $this->getGrossAmount()->getCurrency()->getCode(),
            'description'      => $this->getDescription(),
            'quantity'         => $this->getQuantity(),
            'tax_name'         => $this->getTaxName(),
            'tax_rate'         => $this->getTaxRate(),
        ];
    }
}
