<?php

namespace SerendipityHQ\Bundle\FeaturesBundle\Model;

use Doctrine\ORM\Mapping as ORM;
use SerendipityHQ\Component\ValueObjects\Currency\Currency;
use SerendipityHQ\Component\ValueObjects\Currency\CurrencyInterface;
use SerendipityHQ\Component\ValueObjects\Money\Money as MoneyValue;
use SerendipityHQ\Component\ValueObjects\Money\Money;
use SerendipityHQ\Component\ValueObjects\Money\MoneyInterface;

/**
 * @ORM\MappedSuperclass
 */
abstract class Invoice implements InvoiceInterface
{
    /**
     * @var CurrencyInterface
     * @ORM\Column(name="currency", type="currency", nullable=false)
     */
    private $currency;

    /**
     * @var \DateTime
     * @ORM\Column(name="issued_on", type="datetime", nullable=false)
     */
    private $issuedOn;

    /**
     * @var array
     * @ORM\Column(name="`lines`", type="json_array", nullable=false)
     */
    private $lines;

    /**
     * @var MoneyInterface
     *
     * @ORM\Column(name="total", type="money", nullable=false)
     */
    private $total;

    /**
     * {@inheritdoc}
     */
    public function __construct($currency)
    {
        if (!$currency instanceof CurrencyInterface) {
            $currency = new Currency($currency);
        }

        $this->currency = $currency;
        $this->lines = [];

        // Set the issue date
        if (null === $this->issuedOn) {
            // Create it with microseconds, so it is possible to use the createdOn to create a unique invoice number (http://stackoverflow.com/a/28937386/1399706)
            $this->issuedOn = \DateTime::createFromFormat('U.u', microtime(true));
        }

        if (null === $this->total) {
            $this->total = new Money(['amount' => 0, 'currency' => $this->getCurrency()]);
        }

        // Generate the Invoice number
        $this->generateNumber();
    }

    /**
     * {@inheritdoc}
     */
    public function addLine(InvoiceLine $line) : InvoiceInterface
    {
        $this->lines[] = $line;

        // Recalculate the total
        if (null === $this->getTotal()) {
            // Set the total to 0 amount
            $this->total = new MoneyValue([
                    'amount' => 0, 'currency' => $line->getAmount()->getCurrency(),
                ]);
        }

        // Initialize total if it is null
        if (null === $this->total) {
            $this->total = new Money(0, $line->getAmount()->getCurrency());
        }

        // Set the new Total
        $this->total = $this->getTotal()->add($line->getAmount());

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrency() : CurrencyInterface
    {
        return $this->currency;
    }

    /**
     * {@inheritdoc}
     */
    public function getIssuedOn() : \DateTime
    {
        return $this->issuedOn;
    }

    /**
     * {@inheritdoc}
     */
    public function getLines() : array
    {
        return $this->lines;
    }

    /**
     * {@inheritdoc}
     */
    public function getTotal() : MoneyInterface
    {
        return $this->total;
    }

    /**
     * @ORM\PostLoad()
     */
    public function __rehydrateLines()
    {
        $lines = [];
        foreach ($this->lines as $line) {
            $lineObject = new InvoiceLine();
            $lineObject->hydrate($line);
            $lines[] = $lineObject;
        }

        $this->lines = $lines;
    }
}
