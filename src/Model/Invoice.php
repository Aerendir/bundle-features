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

use Doctrine\ORM\Mapping as ORM;
use Money\Currency;
use function Safe\sprintf;
use SerendipityHQ\Component\ValueObjects\Money\Money;
use SerendipityHQ\Component\ValueObjects\Money\MoneyInterface;

/**
 * @ORM\MappedSuperclass
 * @ORM\HasLifecycleCallbacks
 */
abstract class Invoice implements InvoiceInterface
{
    private const SECTION_DEFAULT = '_default';

    /**
     * @var Currency
     * @ORM\Column(name="currency", type="currency")
     */
    private $currency;

    /**
     * @var \DateTime
     * @ORM\Column(name="issued_on", type="datetime")
     */
    private $issuedOn;

    /**
     * @var array
     * @ORM\Column(name="`sections`", type="json")
     */
    private $sections;

    /**
     * @var MoneyInterface
     *
     * @ORM\Column(name="gross_total", type="money")
     */
    private $grossTotal;

    /**
     * @var MoneyInterface
     *
     * @ORM\Column(name="net_total", type="money")
     */
    private $netTotal;

    /**
     * {@inheritdoc}
     */
    public function __construct($currency)
    {
        if ( ! $currency instanceof Currency) {
            $currency = new Currency($currency);
        }

        $this->currency = $currency;
        $this->sections = [];

        // Set the issue date
        if (null === $this->issuedOn) {
            // Create it with microseconds, so it is possible to use the createdOn to create a unique invoice number (http://stackoverflow.com/a/28937386/1399706)
            $this->issuedOn = \DateTime::createFromFormat('U.u', \microtime(true));
        }

        if (null === $this->grossTotal) {
            $this->grossTotal = new Money([MoneyInterface::BASE_AMOUNT => 0, MoneyInterface::CURRENCY => $this->getCurrency()]);
        }

        if (null === $this->netTotal) {
            $this->netTotal = new Money([MoneyInterface::BASE_AMOUNT => 0, MoneyInterface::CURRENCY => $this->getCurrency()]);
        }

        // Generate the Invoice number
        $this->generateNumber();
    }

    /**
     * {@inheritdoc}
     */
    public function getHeader(): ?InvoiceSectionHeader
    {
        return $this->sections[self::SECTION_DEFAULT]->getHeader();
    }

    /**
     * {@inheritdoc}
     */
    public function hasHeader(): bool
    {
        return $this->sections[self::SECTION_DEFAULT]->hasHeader();
    }

    /**
     * {@inheritdoc}
     */
    public function removeHeader()
    {
        return $this->sections[self::SECTION_DEFAULT]->removeHeader();
    }

    /**
     * {@inheritdoc}
     */
    public function setHeader(InvoiceSectionHeader $header)
    {
        $this->sections[self::SECTION_DEFAULT]->setHeader($header);
    }

    /**
     * {@inheritdoc}
     */
    public function addLine(InvoiceLine $line, string $id = null): InvoiceInterface
    {
        if (false === isset($this->sections[self::SECTION_DEFAULT])) {
            $this->sections[self::SECTION_DEFAULT] = new InvoiceSection($this->getCurrency());
        }

        $this->sections[self::SECTION_DEFAULT]->addLine($line, $id);

        $this->recalculateTotal();

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getLine($id): InvoiceLine
    {
        return $this->sections[self::SECTION_DEFAULT]->getLine($id);
    }

    /**
     * {@inheritdoc}
     */
    public function getLines(): array
    {
        return $this->sections[self::SECTION_DEFAULT]->getLines();
    }

    /**
     * {@inheritdoc}
     */
    public function hasLine($id): bool
    {
        return $this->sections[self::SECTION_DEFAULT]->hasLine($id);
    }

    /**
     * {@inheritdoc}
     */
    public function removeLine($id)
    {
        $return = $this->sections[self::SECTION_DEFAULT]->removeLine($id);

        $this->recalculateTotal();

        return $return;
    }

    /**
     * {@inheritdoc}
     */
    public function addSection(InvoiceSection $section, string $id = null): InvoiceInterface
    {
        if ($this->getCurrency()->getCode() !== $section->getCurrency()->getCode()) {
            throw new \LogicException(sprintf('The Sections and the Invoice to which you add it MUST have the same currency code. Invoice has code "%s" while Section has code "%s".', $this->getCurrency()->getCode(), $section->getCurrency()->getCode()));
        }

        switch (\gettype($id)) {
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
                throw new \InvalidArgumentException(sprintf('Invalid $id type. Accepted types are "string, "integer" and "null". You passed "%s".', \gettype($id)));
        }

        // Set the new Total
        $this->grossTotal = $this->getGrossTotal()->add($section->getGrossTotal());
        $this->netTotal   = $this->getNetTotal()->add($section->getNetTotal());

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getSection($id): ?InvoiceSection
    {
        if (self::SECTION_DEFAULT === $id && false === isset($this->sections[self::SECTION_DEFAULT])) {
            $this->sections[self::SECTION_DEFAULT] = new InvoiceSection($this->getCurrency());
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
        if (false === \is_string($id) && false === \is_int($id)) {
            throw new \InvalidArgumentException(sprintf('Only strings or integers are accepted as $id. "%s" passed.', \gettype($id)));
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
    public function getCurrency(): Currency
    {
        return $this->currency;
    }

    /**
     * {@inheritdoc}
     *
     * @return \DateTime|\DateTimeImmutable
     */
    public function getIssuedOn(): \DateTimeInterface
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
    public function jsonSerialize(): array
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
        $this->grossTotal = new Money([MoneyInterface::BASE_AMOUNT => 0, MoneyInterface::CURRENCY => $this->getCurrency()]);
        $this->netTotal   = new Money([MoneyInterface::BASE_AMOUNT => 0, MoneyInterface::CURRENCY => $this->getCurrency()]);

        /** @var InvoiceSection $section */
        foreach ($this->getSections() as $section) {
            $this->grossTotal = $this->grossTotal->add($section->getGrossTotal());
            $this->netTotal   = $this->netTotal->add($section->getNetTotal());
        }
    }
}
