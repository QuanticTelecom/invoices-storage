<?php

namespace QuanticTelecom\InvoicesStorage\Contracts;

use QuanticTelecom\Invoices\AbstractInvoice;

/**
 * Interface InvoiceRepositoryInterface.
 */
interface InvoiceRepositoryInterface
{
    /**
     * Fetch one invoice by his ID.
     *
     * @param $id
     *
     * @return AbstractInvoice
     */
    public function get($id);

    /**
     * Fetch all invoices.
     *
     * @return AbstractInvoice[]
     */
    public function getAll();

    /**
     * Save an invoice.
     *
     * @param AbstractInvoice $invoice
     */
    public function save(AbstractInvoice $invoice);
}
