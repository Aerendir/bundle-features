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
     * @ORM\Column(name="`sections`", type="json_array", nullable=false)
     */
    private $sections;

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
        $this->sections = [];

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
    public function getHeader()
    {
        if ($this->hasHeader())
            return $this->sections['_default']['_header'];

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function hasHeader()
    {
        return isset($this->sections['_default']['_header']);
    }

    /**
     * {@inheritdoc}
     */
    public function removeHeader()
    {
        if ($this->hasHeader()) {
            $return = $this->getHeader();
            unset($this->sections['_default']['_header']);
            return $return;
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function setHeader(InvoiceLineHeader $header)
    {
        $this->sections['_default']['_header'] = $header;
    }

    /**
     * {@inheritdoc}
     */
    public function addLine(InvoiceLine $line, string $id = null) : InvoiceInterface
    {
        switch (gettype($id)) {
            case 'string':
            case 'integer':
                if ('_header' === $id) {
                    throw new \InvalidArgumentException('You cannot add a line with id "_header" as it is a reserved word.');
                }

            if ($this->hasLine($id))
                throw new \LogicException(sprintf('The section "%s" already exists. You cannot add it again', $id));

                $this->sections['_default'][$id] = $line;
                break;
            case 'NULL':
                $this->sections['_default'][] = $line;
                break;
            default:
                throw new \InvalidArgumentException(sprintf('Invalid $id type. Accepted types are "string, "integer" and "null". You passed "%s".', gettype($id)));
        }

        // Set the new Total
        $this->total = $this->getTotal()->add($line->getAmount());

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getLine($id)
    {
        return isset($this->sections['_default'][$id]) ? $this->sections['_default'][$id] : null;
    }

    /**
     * {@inheritdoc}
     */
    public function getLines() : array
    {
        return $this->sections['_default'];
    }

    /**
     * @param string|int $id
     * @return bool
     */
    public function hasLine($id)
    {
        if (false === is_string($id) && false === is_int($id)) {
            throw new \InvalidArgumentException(sprintf('Only strings or integers are accepted as $id. "%s" passed.', gettype($id)));
        }

        return isset($this->sections['_default'][$id]);
    }

    /**
     * @param string| int $id
     * @return bool|InvoiceLine The removed InvoiceLine or false if it isn't found.
     */
    public function removeLine($id)
    {
        if ($this->hasLine($id)) {
            $return = $this->sections['_default'][$id];
            unset($this->sections['_default'][$id]);
            return $return;
        }

        return false;
    }

    /**
     * @param InvoiceInterface $section
     * @param string|null $id
     * @return $this
     */
    public function addSection(InvoiceInterface $section, string $id = null)
    {
        switch (gettype($id)) {
            case 'string':
            case 'integer':
                if ('_default' === $id) {
                    throw new \InvalidArgumentException('You cannot add a section with id "_default" as it is a reserved word.');
                }

                if ($this->hasSection($id))
                    throw new \LogicException(sprintf('The section "%s" already exists. You cannot add it again', $id));

                $this->sections[$id] = $section;
                break;
            case 'NULL':
                $this->sections[] = $section;
                break;
            default:
                throw new \InvalidArgumentException(sprintf('Invalid $id type. Accepted types are "string, "integer" and "null". You passed "%s".', gettype($id)));
        }

        // Set the new Total
        $this->total = $this->getTotal()->add($section->getTotal());

        return $this;
    }

    /**
     * Do not typecast as it can be also an integer
     * {@inheritdoc}
     */
    public function getSection($id)
    {
        return isset($this->sections[$id]) ? $this->sections[$id] : null;
    }

    /**
     * {@inheritdoc}
     */
    public function getSections() : array
    {
        return $this->sections;
    }

    /**
     * {@inheritdoc}
     */
    public function hasSection($id)
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
            return $return;
        }

        return false;
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
    public function getTotal() : MoneyInterface
    {
        return $this->total;
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
     * @ORM\PostLoad()
     */
    public function jsonUnserialize()
    {
        $sections = [];
        foreach ($this->sections[0] as $sectionId => $section) {
            $hydtratingSectionClass = get_class($this);

            /** @var InvoiceInterface $hydrtratingSection */
            $hydrtratingSection = new $hydtratingSectionClass($this->currency);

            foreach ($section as $lineId => $line) {
                $lineObject = new InvoiceLine();
                $lineObject->hydrate($line);
                $hydrtratingSection->addLine($lineObject, $lineId);
            }

            $sections[$sectionId] = $hydrtratingSection;
        }

        $this->sections = $sections;
    }
}
