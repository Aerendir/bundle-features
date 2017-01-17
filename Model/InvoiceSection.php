<?php

namespace SerendipityHQ\Bundle\FeaturesBundle\Model;
use SerendipityHQ\Component\ValueObjects\Currency\CurrencyInterface;
use SerendipityHQ\Component\ValueObjects\Money\Money;
use SerendipityHQ\Component\ValueObjects\Money\MoneyInterface;

/**
 * Represents a section into the Invoice.
 */
class InvoiceSection implements \JsonSerializable
{
    /** @var  CurrencyInterface $currency */
    private $currency;

    /** @var  InvoiceSectionHeader */
    private $header;

    /** @var  InvoiceLine[] */
    private $lines = [];

    /** @var  MoneyInterface $total */
    private $total;

    /**
     * @param CurrencyInterface $currency
     */
    public function __construct(CurrencyInterface $currency)
    {
        $this->currency = $currency;
        $this->total    = new Money(['amount' => 0, 'currency' => $currency]);
    }

    /**
     * @return CurrencyInterface
     */
    public function getCurrency() : CurrencyInterface
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
    public function hasHeader() : bool
    {
        return isset($this->header);
    }

    /**
     * @return InvoiceSection
     */
    public function removeHeader() : InvoiceSection
    {
        $this->header = null;

        return $this;
    }

    /**
     * @param InvoiceSectionHeader $header
     * @return InvoiceSection
     */
    public function setHeader(InvoiceSectionHeader $header) : InvoiceSection
    {
        $this->header = $header;

        return $this;
    }

    /**
     * @param InvoiceLine $line
     * @param string|null $id
     * @return InvoiceSection
     */
    public function addLine(InvoiceLine $line, string $id = null) : InvoiceSection
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
        $this->total = $this->getTotal()->add($line->getAmount());

        return $this;
    }

    /**
     * @param string|int $id
     * @return null|InvoiceLine
     */
    public function getLine($id)
    {
        return $this->lines[$id] ?? null;
    }

    /**
     * @return array
     */
    public function getLines() : array
    {
        return $this->lines;
    }

    /**
     * @param string|int $id
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
    public function getTotal() : MoneyInterface
    {
        return $this->total;
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
        $this->total = new Money(['amount' => 0, 'currency' => $this->getCurrency()]);

        /** @var InvoiceLine $section */
        foreach ($this->getLines() as $line) {
            $this->total = $this->total->add($line->getTotal());
        }
    }
}
