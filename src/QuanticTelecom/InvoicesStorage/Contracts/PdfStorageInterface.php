<?php namespace QuanticTelecom\InvoicesStorage\Contracts;

use QuanticTelecom\Invoices\Contracts\InvoiceInterface;

interface PdfStorageInterface
{
    /**
     * Get the local path for an invoice.
     *
     * @param InvoiceInterface $invoice
     *
     * @return string
     */
    public function getStoragePath(InvoiceInterface $invoice);
}
