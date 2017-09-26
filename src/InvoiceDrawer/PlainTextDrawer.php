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
    public function draw(InvoiceInterface $invoice): array
    {
        $detailsTables = [];
        foreach ($invoice->getSections() as $sectionId => $section) {
            $detailsTables[$sectionId] = $this->buildInvoiceTextTable($section);
        }

        $equals_separator       = $this->drawSeparator('=', $this->tableWidth);
        $dash_separator         = $this->drawSeparator('-', $this->tableWidth);
        $equals_separator_short = $this->drawSeparator('=', $this->tableWidth - round(35 % $this->tableWidth));
        $equals_separator_short = $this->drawSeparator(' ', $this->tableWidth - iconv_strlen($equals_separator_short)) . $equals_separator_short;

        $detailsTable = '';
        foreach ($detailsTables as $sectionId => $sectionContent) {
            if ($invoice->getSection($sectionId)->hasHeader()) {
                $detailsTable .= '_default' === $sectionId ? '' : $dash_separator . "\n";
                $detailsTable .= $invoice->getSection($sectionId)->getHeader()->getHeader() . "\n";
                $detailsTable .= '_default' === $sectionId ? $equals_separator : $dash_separator;
                $detailsTable .= "\n";
            }

            $detailsTable .= $sectionContent . "\n";
        }

        $total_gross_amount = mb_strtoupper($this->getTranslator()->trans('shq_features.invoice.total.label', [], 'Invoice'))
            . ' ' . $this->getCurrencyFormatter()->formatCurrency($invoice->getNetTotal()->getHumanAmount(), $invoice->getGrossTotal()->getCurrency())
            . ' (' . $this->getCurrencyFormatter()->formatCurrency($invoice->getGrossTotal()->getHumanAmount(), $invoice->getNetTotal()->getCurrency()) . ')';
        /*
        $total_gross_amount = mb_strtoupper($this->getTranslator()->trans('shq_features.invoice.total.label', [], 'Invoice'))
            . ' ' . $invoice->getNetTotal()->getHumanAmount()
            . ' (' . $invoice->getGrossTotal()->getHumanAmount() . ')';
        */
        $total_gross_amount = $this->drawSeparator(' ', $this->tableWidth - iconv_strlen($total_gross_amount)) . $total_gross_amount;

        $data = [
            'invoice'                => $invoice,
            'details_table'          => $detailsTable,
            'dot_separator'          => $this->drawSeparator('.', $this->tableWidth),
            'equals_separator'       => $this->drawSeparator('=', $this->tableWidth),
            'equals_separator_short' => $equals_separator_short,
            'total_amount'           => $total_gross_amount,
        ];

        return $data;
    }

    /**
     * @return int
     */
    public function getTableWidth(): int
    {
        return $this->tableWidth;
    }

    /**
     * @param InvoiceSection $section
     *
     * @return string
     */
    private function buildInvoiceTextTable(InvoiceSection $section): string
    {
        $tableData = [
            [
                'quantity'        => mb_strtoupper($this->getTranslator()->trans('shq_features.invoice.quantity.label', [], 'Invoice')),
                'description'     => mb_strtoupper($this->getTranslator()->trans('shq_features.invoice.description.label', [], 'Invoice')),
                'baseAmount'      => mb_strtoupper($this->getTranslator()->trans('shq_features.invoice.amount.label', [], 'Invoice')),
            ],
        ];

        /** @var InvoiceLine $line */
        foreach ($section->getLines() as $line) {
            $lineData = [
                'quantity'        => 0 === $line->getQuantity() ? 'N/A' : $line->getQuantity(),
                'description'     => $line->getDescription(),
                'baseAmount'      => $this->getCurrencyFormatter()->formatCurrency($line->getNetAmount()->getHumanAmount(), $line->getNetAmount()->getCurrency())
                . ' (' . $this->getCurrencyFormatter()->formatCurrency($line->getGrossAmount()->getHumanAmount(), $line->getGrossAmount()->getCurrency()) . ')',
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
                'baseAmount' => [
                    'align'     => 'right',
                    'min_width' => 18,
                ],
            ],
        ];

        $return           = $table->render($options);
        $this->tableWidth = $table->getTableWidth();

        return $return;
    }

    /**
     * @param string $char
     * @param int    $length
     *
     * @return string
     */
    private function drawSeparator($char, $length): string
    {
        $separator = '';

        for ($i = 0; $i < $length; ++$i) {
            $separator .= $char;
        }

        return $separator;
    }
}
