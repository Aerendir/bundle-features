<?php

namespace SerendipityHQ\Bundle\FeaturesBundle\Model;

use SerendipityHQ\Component\ValueObjects\Currency\Currency;
use SerendipityHQ\Component\ValueObjects\Money\Money;

/**
 * This entity will never be persisted.
 *
 * The lines are serialized by Issue when persisted to the database and deserialized when get from it.
 */
class InvoiceLine implements \JsonSerializable
{
    /** @var Money $grossAmount */
    private $grossAmount;

    /** @var Money $netAmount */
    private $netAmount;

    /** @var string $description */
    private $description;

    /** @var string $quantity */
    private $quantity;

    /** @var  string $taxName */
    private $taxName;

    /** @var  float $taxRate */
    private $taxRate;

    /**
     * @return Money
     */
    public function getGrossAmount() : Money
    {
        return $this->grossAmount;
    }

    /**
     * @return Money
     */
    public function getNetAmount() : Money
    {
        return $this->netAmount;
    }

    /**
     * @return string
     */
    public function getDescription() : string
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
    public function getTaxName() : string
    {
        return $this->taxName;
    }

    /**
     * @return float
     */
    public function getTaxRate() : float
    {
        return $this->taxRate;
    }

    /**
     * @param Money $grossAmount
     *
     * @return self
     */
    public function setGrossAmount(Money $grossAmount) : self
    {
        $this->grossAmount = $grossAmount;

        return $this;
    }

    /**
     * @param Money $netAmount
     *
     * @return self
     */
    public function setNetAmount(Money $netAmount) : self
    {
        $this->netAmount = $netAmount;

        return $this;
    }

    /**
     * @param string $description
     *
     * @return self
     */
    public function setDescription(string $description) : self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @param int|null $quantity
     * @return InvoiceLine
     */
    public function setQuantity($quantity) : self
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * @param string $taxName
     * @return InvoiceLine
     */
    public function setTaxName(string $taxName) : self
    {
        $this->taxName = $taxName;

        return $this;
    }

    /**
     * @param float $taxRate
     * @return InvoiceLine
     */
    public function setTaxRate(float $taxRate) : self
    {
        $this->taxRate = $taxRate;

        return $this;
    }

    /**
     * @return array
     */
    public function jsonSerialize() : array
    {
        return $this->__toArray();
    }

    /**
     * @param array $data
     */
    public function hydrate(array $data)
    {
        $grossAmount = new Money(['amount' => (int) $data['gross_amount'], 'currency' => new Currency($data['currency'])]);
        $netAmount = new Money(['amount' => (int) $data['net_amount'], 'currency' => new Currency($data['currency'])]);
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
            'net_amount' => $this->getNetAmount()->getAmount(),
            'currency' => $this->getGrossAmount()->getCurrency()->getCurrencyCode(),
            'description' => $this->getDescription(),
            'quantity' => $this->getQuantity(),
            'tax_name' => $this->getTaxName(),
            'tax_rate' => $this->getTaxRate()
        ];
    }
}
