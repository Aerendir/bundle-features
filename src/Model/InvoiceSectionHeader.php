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

namespace SerendipityHQ\Bundle\FeaturesBundle\Model;

/**
 * This entity will never be persisted but only serialized.
 *
 * An Header line to use to draw an header in the invoice lines.
 */
final class InvoiceSectionHeader implements \JsonSerializable
{
    private string $header;

    public function __construct(string $header)
    {
        $this->header = $header;
    }

    public function __toArray(): array
    {
        return [
            'header' => $this->getHeader(),
        ];
    }

    public function getHeader(): string
    {
        return $this->header;
    }

    public function jsonSerialize(): array
    {
        return $this->__toArray();
    }

    public function hydrate(array $data): void
    {
        $this->header = $data['header'];
    }
}
