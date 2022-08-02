<?php

declare(strict_types=1);

/*
 * This file is part of the Serendipity HQ Features Bundle.
 *
 * Copyright (c) Adamo Aerendir Crespi <aerendir@serendipityhq.com>.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SerendipityHQ\Bundle\FeaturesBundle\InvoiceDrawer;

use SerendipityHQ\Bundle\FeaturesBundle\Model\InvoiceInterface;
use SerendipityHQ\Bundle\FeaturesBundle\Model\InvoiceLine;
use SerendipityHQ\Bundle\FeaturesBundle\Model\InvoiceSection;
use SerendipityHQ\Component\PHPTextMatrix\PHPTextMatrix;

/**
 * Formats an Invoice in Plain text.
 */
final class PlainTextDrawer extends AbstractInvoiceDrawer
{
    private int $tableWidth;

    public function draw(InvoiceInterface $invoice): array
    {
        $detailsTables = [];
        foreach ($invoice->getSections() as $sectionId => $section) {
            $detailsTables[$sectionId] = $this->buildInvoiceTextTable($section);
        }

        $equalsSeparator       = $this->drawSeparator('=', $this->tableWidth);
        $dashSeparator         = $this->drawSeparator('-', $this->tableWidth);
        $equalsSeparatorShort  = $this->drawSeparator('=', $this->tableWidth - (int) \round(35 % $this->tableWidth));
        $equalsSeparatorShort  = $this->drawSeparator(' ', $this->tableWidth - \iconv_strlen($equalsSeparatorShort)) . $equalsSeparatorShort;

        $detailsTable = '';
        foreach ($detailsTables as $sectionId => $sectionContent) {
            if ($invoice->getSection($sectionId)->hasHeader()) {
                $detailsTable .= '_default' === $sectionId ? '' : $dashSeparator . "\n";
                $detailsTable .= $invoice->getSection($sectionId)->getHeader()->getHeader() . "\n";
                $detailsTable .= '_default' === $sectionId ? $equalsSeparator : $dashSeparator;
                $detailsTable .= "\n";
            }

            $detailsTable .= $sectionContent . "\n";
        }

        $totalGrossAmount = \mb_strtoupper($this->getTranslator()->trans('shq_features.invoice.total.label', [], 'Invoice'))
            . ' ' . $this->getCurrencyFormatter()->formatCurrency((float) $invoice->getNetTotal()->getHumanAmount(), $invoice->getGrossTotal()->getCurrency()->__toString())
            . ' (' . $this->getCurrencyFormatter()->formatCurrency((float) $invoice->getGrossTotal()->getHumanAmount(), $invoice->getNetTotal()->getCurrency()->__toString()) . ')';
        /*
        $total_gross_amount = mb_strtoupper($this->getTranslator()->trans('shq_features.invoice.total.label', [], 'Invoice'))
            . ' ' . $invoice->getNetTotal()->getHumanAmount()
            . ' (' . $invoice->getGrossTotal()->getHumanAmount() . ')';
        */
        $totalGrossAmount = $this->drawSeparator(' ', $this->tableWidth - \iconv_strlen($totalGrossAmount)) . $totalGrossAmount;

        return [
            'invoice'                => $invoice,
            'details_table'          => $detailsTable,
            'dot_separator'          => $this->drawSeparator('.', $this->tableWidth),
            'equals_separator'       => $this->drawSeparator('=', $this->tableWidth),
            'equals_separator_short' => $equalsSeparatorShort,
            'total_amount'           => $totalGrossAmount,
        ];
    }

    public function getTableWidth(): int
    {
        return $this->tableWidth;
    }

    private function buildInvoiceTextTable(InvoiceSection $section): string
    {
        $tableData = [
            [
                'quantity'    => \mb_strtoupper($this->getTranslator()->trans('shq_features.invoice.quantity.label', [], 'Invoice')),
                'description' => \mb_strtoupper($this->getTranslator()->trans('shq_features.invoice.description.label', [], 'Invoice')),
                'baseAmount'  => \mb_strtoupper($this->getTranslator()->trans('shq_features.invoice.amount.label', [], 'Invoice')),
            ],
        ];

        /** @var InvoiceLine $line */
        foreach ($section->getLines() as $line) {
            $lineData = [
                'quantity'    => 0 === $line->getQuantity() ? 'N/A' : $line->getQuantity(),
                'description' => $line->getDescription(),
                'baseAmount'  => $this->getCurrencyFormatter()->formatCurrency((float) $line->getNetAmount()->getHumanAmount(), $line->getNetAmount()->getCurrency()->__toString())
                . ' (' . $this->getCurrencyFormatter()->formatCurrency((float) $line->getGrossAmount()->getHumanAmount(), $line->getGrossAmount()->getCurrency()->__toString()) . ')',
            ];
            $tableData[] = $lineData;
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

    private function drawSeparator(string $char, int $length): string
    {
        $separator = '';

        for ($i = 0; $i < $length; ++$i) {
            $separator .= $char;
        }

        return $separator;
    }
}
