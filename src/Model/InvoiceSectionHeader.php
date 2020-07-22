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

/**
 * This entity will never be persisted but only serialized.
 *
 * An Header line to use to draw an header in the invoice lines.
 */
class InvoiceSectionHeader implements \JsonSerializable
{
    /** @var string $header */
    private $header;

    /**
     * @param string $header
     */
    public function __construct(string $header)
    {
        $this->header = $header;
    }

    /**
     * @return string
     */
    public function getHeader()
    {
        return $this->header;
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
        $this->header = $data['header'];
    }

    /**
     * @return array
     */
    public function __toArray()
    {
        return [
            'header' => $this->getHeader(),
        ];
    }
}
