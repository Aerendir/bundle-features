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
    /** @var Money $amount */
    private $amount;

    /** @var string $description */
    private $description;

    /** @var string */
    private $quantity;

    /**
     * @return Money
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return string
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * @param Money $amount
     *
     * @return $this
     */
    public function setAmount(Money $amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * @param $description
     *
     * @return $this
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @param string $quantity
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->__toArray();
    }

    /**
     * @param array $data
     */
    public function hydrate(array $data)
    {
        $amount = new Money(['amount' => (int) $data['amount'], 'currency' => new Currency($data['currency'])]);
        $this->setDescription($data['description']);
        $this->setAmount($amount);
        $this->setQuantity($data['quantity']);
    }

    /**
     * @return array
     */
    public function __toArray()
    {
        return [
            'amount'      => $this->getAmount()->getAmount(),
            'currency'    => $this->getAmount()->getCurrency()->getCurrencyCode(),
            'description' => $this->getDescription(),
            'quantity'    => $this->getQuantity(),
        ];
    }
}
