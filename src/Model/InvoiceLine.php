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

use Money\Currency;
use SerendipityHQ\Component\ValueObjects\Money\Money;
use SerendipityHQ\Component\ValueObjects\Money\MoneyInterface;

/**
 * This entity will never be persisted.
 *
 * The lines are serialized by Issue when persisted to the database and deserialized when get from it.
 */
class InvoiceLine implements \JsonSerializable
{
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

    /**
     * @return MoneyInterface
     */
    public function getGrossAmount(): MoneyInterface
    {
        return $this->grossAmount;
    }

    /**
     * @return MoneyInterface
     */
    public function getNetAmount(): MoneyInterface
    {
        return $this->netAmount;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @return int|null
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * @return string
     */
    public function getTaxName(): string
    {
        return $this->taxName;
    }

    /**
     * @return float
     */
    public function getTaxRate(): float
    {
        return $this->taxRate;
    }

    /**
     * @param MoneyInterface$grossAmount
     *
     * @return self
     */
    public function setGrossAmount(MoneyInterface $grossAmount): self
    {
        $this->grossAmount = $grossAmount;

        return $this;
    }

    /**
     * @param MoneyInterface$netAmount
     *
     * @return self
     */
    public function setNetAmount(MoneyInterface $netAmount): self
    {
        $this->netAmount = $netAmount;

        return $this;
    }

    /**
     * @param string $description
     *
     * @return self
     */
    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @param int|null $quantity
     *
     * @return InvoiceLine
     */
    public function setQuantity($quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * @param string $taxName
     *
     * @return InvoiceLine
     */
    public function setTaxName(string $taxName): self
    {
        $this->taxName = $taxName;

        return $this;
    }

    /**
     * @param float $taxRate
     *
     * @return InvoiceLine
     */
    public function setTaxRate(float $taxRate): self
    {
        $this->taxRate = $taxRate;

        return $this;
    }

    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        return $this->__toArray();
    }

    /**
     * @param array $data
     */
    public function hydrate(array $data)
    {
        $grossAmount = new Money(['baseAmount' => (int) $data['gross_amount'], 'currency' => new Currency($data['currency'])]);
        $netAmount   = new Money(['baseAmount' => (int) $data['net_amount'], 'currency' => new Currency($data['currency'])]);
        $this->setDescription($data['description']);
        $this->setGrossAmount($grossAmount);
        $this->setNetAmount($netAmount);
        $this->setQuantity((int) $data['quantity']);
        $this->setTaxName($data['tax_name']);
        $this->setTaxRate($data['tax_rate']);
    }

    /**
     * @return array
     */
    public function __toArray()
    {
        return [
            'gross_amount' => $this->getGrossAmount()->getAmount(),
            'net_amount'   => $this->getNetAmount()->getAmount(),
            'currency'     => $this->getGrossAmount()->getCurrency()->getCode(),
            'description'  => $this->getDescription(),
            'quantity'     => $this->getQuantity(),
            'tax_name'     => $this->getTaxName(),
            'tax_rate'     => $this->getTaxRate(),
        ];
    }
}
