<?php

namespace SerendipityHQ\Bundle\FeaturesBundle\InvoiceDrawer;

use SerendipityHQ\Bundle\FeaturesBundle\Model\InvoiceInterface;
use SerendipityHQ\Bundle\FeaturesBundle\Model\InvoiceLine;
use SerendipityHQ\Bundle\FeaturesBundle\Model\InvoiceSection;
use SerendipityHQ\Component\PHPTextMatrix\PHPTextMatrix;

/**
 * Formats an Invoice in Plain text.
 */
class PlainTextDrawer extends AbstractInvoiceDrawer
{
    /** @var int $tableWidth */
    private $tableWidth;

    /**
     * @param InvoiceInterface $invoice
     *
     * @return array
     */
    public function draw(InvoiceInterface $invoice) : array
    {
        $detailsTables = [];
        foreach ($invoice->getSections() as $sectionId => $section) {
            $detailsTables[$sectionId] = $this->buildInvoiceTextTable($section);
        }

        $dash_separator         = $this->drawSeparator('-', $this->tableWidth);
        $equals_separator_short = $this->drawSeparator('=', $this->tableWidth - (40 % $this->tableWidth));
        $equals_separator_short = $this->drawSeparator(' ', $this->tableWidth - iconv_strlen($equals_separator_short)) . $equals_separator_short;

        $detailsTable = '';
        foreach ($detailsTables as $sectionId => $sectionContent) {
            if ($invoice->getSection($sectionId)->hasHeader()) {
                $detailsTable .= $invoice->getSection($sectionId)->getHeader()->getHeader() . "\n";
                $detailsTable .= $dash_separator . "\n";
            }

            $detailsTable .= $sectionContent . "\n";
        }

        $total_amount = $this->getTranslator()->trans('company.invoice.total.label', [], 'company')
            . ' ' . $this->getCurrencyFormatter()->formatCurrency($invoice->getTotal()->getConvertedAmount(), $invoice->getTotal()->getCurrency());
        $total_amount = $this->drawSeparator(' ', $this->tableWidth - iconv_strlen($total_amount)) . $total_amount;

        $data = [
            'invoice'                => $invoice,
            'details_table'          => $detailsTable,
            'dot_separator'          => $this->drawSeparator('.', $this->tableWidth),
            'equals_separator'       => $this->drawSeparator('=', $this->tableWidth),
            'equals_separator_short' => $equals_separator_short,
            'total_amount'           => $total_amount,
        ];

        return $data;
    }

    /**
     * @return int
     */
    public function getTableWidth() : int
    {
        return $this->tableWidth;
    }

    /**
     * @param InvoiceSection $section
     *
     * @return string
     */
    private function buildInvoiceTextTable(InvoiceSection $section) : string
    {
        $tableData = [
            [
                'quantity'    => $this->getTranslator()->trans('company.invoice.quantity.label', [], 'company'),
                'description' => $this->getTranslator()->trans('company.invoice.description.label', [], 'company'),
                'amount'      => $this->getTranslator()->trans('company.invoice.amount.label', [], 'company'),
            ],
        ];

        /** @var InvoiceLine $line */
        foreach ($section->getLines() as $line) {
            $lineData = [
                'quantity'    => $line->getQuantity(),
                'description' => $line->getDescription(),
                'amount'      => $this->getCurrencyFormatter()->formatCurrency($line->getAmount()->getConvertedAmount(), $line->getAmount()->getCurrency()),
            ];
            array_push($tableData, $lineData);
        }

        $table = new PHPTextMatrix($tableData);

        $options = [
            'has_header'        => true,
            'cells_padding'     => [0],
            'sep_head_v'        => ' ',
            'sep_head_x'        => ' ',
            'sep_head_h'        => '-',
            'sep_v'             => ' ',
            'sep_x'             => ' ',
            'sep_h'             => ' ',
            'show_head_top_sep' => false,
            'columns'           => [
                'quantity' => [
                    'min_width' => 10,
                    'max_width' => 10,
                ],
                'description' => [
                    'max_width' => 32,
                    'min_width' => 32,
                ],
                'amount' => [
                    'align'     => 'right',
                    'min_width' => 12,
                ],
            ],
        ];

        $return           = $table->render($options);
        $this->tableWidth = $table->getTableWidth();

        return $return;
    }

    /**
     * @param InvoiceInterface $section
     */
    private function getSectionData(InvoiceInterface $section) {

    }

    /**
     * @param string $char
     * @param int    $length
     *
     * @return string
     */
    private function drawSeparator($char, $length) : string
    {
        $separator = '';

        for ($i = 0; $i < $length; ++$i) {
            $separator .= $char;
        }

        return $separator;
    }
}
