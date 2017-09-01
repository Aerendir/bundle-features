<?php

namespace SerendipityHQ\Bundle\FeaturesBundle\Model;

use Doctrine\ORM\Mapping as ORM;
use SerendipityHQ\Component\ValueObjects\Currency\Currency;
use SerendipityHQ\Component\ValueObjects\Currency\CurrencyInterface;
use SerendipityHQ\Component\ValueObjects\Money\Money;
use SerendipityHQ\Component\ValueObjects\Money\MoneyInterface;

/**
 * @ORM\MappedSuperclass
 * @ORM\HasLifecycleCallbacks
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
     * @ORM\Column(name="`sections`", type="json", nullable=false)
     */
    private $sections;

    /**
     * @var MoneyInterface
     *
     * @ORM\Column(name="gross_total", type="money", nullable=false)
     */
    private $grossTotal;

    /**
     * @var MoneyInterface
     *
     * @ORM\Column(name="net_total", type="money", nullable=false)
     */
    private $netTotal;

    /**
     * {@inheritdoc}
     */
    public function __construct($currency)
    {
        if (!$currency instanceof CurrencyInterface) {
            $currency = new Currency($currency);
        }

        $this->currency = $currency;
        $this->sections = [];

        // Set the issue date
        if (null === $this->issuedOn) {
            // Create it with microseconds, so it is possible to use the createdOn to create a unique invoice number (http://stackoverflow.com/a/28937386/1399706)
            $this->issuedOn = \DateTime::createFromFormat('U.u', microtime(true));
        }

        if (null === $this->grossTotal) {
            $this->grossTotal = new Money(['amount' => 0, 'currency' => $this->getCurrency()]);
        }

        if (null === $this->netTotal) {
            $this->netTotal = new Money(['amount' => 0, 'currency' => $this->getCurrency()]);
        }

        // Generate the Invoice number
        $this->generateNumber();
    }

    /**
     * {@inheritdoc}
     */
    public function getHeader()
    {
        return $this->sections['_default']->getHeader();
    }

    /**
     * {@inheritdoc}
     */
    public function hasHeader()
    {
        return $this->sections['_default']->hasHeader();
    }

    /**
     * {@inheritdoc}
     */
    public function removeHeader()
    {
        return $this->sections['_default']->removeHeader();
    }

    /**
     * {@inheritdoc}
     */
    public function setHeader(InvoiceSectionHeader $header)
    {
        $this->sections['_default']->setHeader($header);
    }

    /**
     * {@inheritdoc}
     */
    public function addLine(InvoiceLine $line, string $id = null): InvoiceInterface
    {
        if (false === isset($this->sections['_default'])) {
            $this->sections['_default'] = new InvoiceSection($this->getCurrency());
        }

        $this->sections['_default']->addLine($line, $id);

        $this->recalculateTotal();

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getLine($id)
    {
        return $this->sections['_default']->getLine($id);
    }

    /**
     * {@inheritdoc}
     */
    public function getLines(): array
    {
        return $this->sections['_default']->getLines();
    }

    /**
     * {@inheritdoc}
     */
    public function hasLine($id)
    {
        return $this->sections['_default']->hasLine($id);
    }

    /**
     * {@inheritdoc}
     */
    public function removeLine($id)
    {
        $return = $this->sections['_default']->removeLine($id);

        $this->recalculateTotal();

        return $return;
    }

    /**
     * {@inheritdoc}
     */
    public function addSection(InvoiceSection $section, string $id = null): self
    {
        if ($this->getCurrency()->getCurrencyCode() !== $section->getCurrency()->getCurrencyCode()) {
            throw new \LogicException(
                sprintf(
                    'The Sections and the Invoice to which you add it MUST have the same currency code. Invoice has code "%s" while Section has code "%s".',
                    $this->getCurrency()->getCurrencyCode(),
                    $section->getCurrency()->getCurrencyCode()
                )
            );
        }

        switch (gettype($id)) {
            case 'string':
            case 'integer':
                if ($this->hasSection($id)) {
                    throw new \LogicException(sprintf('The section "%s" already exists. You cannot add it again', $id));
                }

                $this->sections[$id] = $section;
                break;
            case 'NULL':
                $this->sections[] = $section;
                break;
            default:
                throw new \InvalidArgumentException(sprintf('Invalid $id type. Accepted types are "string, "integer" and "null". You passed "%s".', gettype($id)));
        }

        // Set the new Total
        $this->grossTotal = $this->getGrossTotal()->add($section->getGrossTotal());
        $this->netTotal = $this->getNetTotal()->add($section->getNetTotal());

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getSection($id)
    {
        if ('_default' === $id && false === isset($this->sections['_default'])) {
            $this->sections['_default'] = new InvoiceSection($this->getCurrency());
        }

        return $this->sections[$id] ?? null;
    }

    /**
     * {@inheritdoc}
     */
    public function getSections(): array
    {
        return $this->sections;
    }

    /**
     * {@inheritdoc}
     */
    public function hasSection($id): bool
    {
        if (false === is_string($id) && false === is_int($id)) {
            throw new \InvalidArgumentException(sprintf('Only strings or integers are accepted as $id. "%s" passed.', gettype($id)));
        }

        return isset($this->sections[$id]);
    }

    /**
     * {@inheritdoc}
     */
    public function removeSection($id)
    {
        if ($this->hasSection($id)) {
            $return = $this->sections[$id];
            unset($this->sections[$id]);

            $this->recalculateTotal();

            return $return;
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrency(): CurrencyInterface
    {
        return $this->currency;
    }

    /**
     * {@inheritdoc}
     */
    public function getIssuedOn(): \DateTime
    {
        return $this->issuedOn;
    }

    /**
     * {@inheritdoc}
     */
    public function getGrossTotal(): MoneyInterface
    {
        return $this->grossTotal;
    }

    /**
     * {@inheritdoc}
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
        return $this->sections;
    }

    /**
     * {@inheritdoc}
     *
     * @ORM\PostLoad()
     */
    public function jsonUnserialize()
    {
        foreach ($this->sections as $sectionId => $section) {
            $hydratingSection = new InvoiceSection($this->getCurrency());

            // Set the Header if it exists
            if (isset($section['_header'])) {
                $header = new InvoiceSectionHeader($section['_header']);
                $hydratingSection->setHeader($header);
            }

            // Add lines
            foreach ($section['_lines'] as $lineId => $line) {
                $lineObject = new InvoiceLine();
                $lineObject->hydrate($line);
                $hydratingSection->addLine($lineObject, $lineId);
            }

            $this->sections[$sectionId] = $hydratingSection;
        }

        $this->recalculateTotal();
    }

    /**
     * Recalculates the total of the invoice.
     */
    private function recalculateTotal()
    {
        $this->grossTotal = new Money(['amount' => 0, 'currency' => $this->getCurrency()]);
        $this->netTotal = new Money(['amount' => 0, 'currency' => $this->getCurrency()]);

        /** @var InvoiceSection $section */
        foreach ($this->getSections() as $section) {
            $this->grossTotal = $this->grossTotal->add($section->getGrossTotal());
            $this->netTotal = $this->netTotal->add($section->getNetTotal());
        }
    }
}
