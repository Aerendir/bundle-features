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

/**
 * Common interface for InvoiceDrawers.
 */
interface InvoiceDrawerInterface
{
    /**
     * @return mixed
     */
    public function draw(InvoiceInterface $invoice): array;
}
