<?php

namespace SerendipityHQ\Bundle\FeaturesBundle\InvoiceDrawer;

use SerendipityHQ\Bundle\FeaturesBundle\Model\InvoiceInterface;

/**
 * Common interface for InvoiceDrawers.
 */
interface InvoiceDrawerInterface
{
    /**
     * @param InvoiceInterface $invoice
     * @return mixed
     */
    public function draw(InvoiceInterface $invoice) : array;
}
