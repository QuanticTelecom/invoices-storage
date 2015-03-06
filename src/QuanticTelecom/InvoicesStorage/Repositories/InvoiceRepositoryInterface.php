<?php namespace QuanticTelecom\InvoicesStorage\Repositories;

use QuanticTelecom\Invoices\AbstractInvoice;

interface InvoiceRepositoryInterface
{
    /**
     * Fetch one invoice by his ID.
     *
     * @param $id
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
     * @return mixed
     */
    public function save(AbstractInvoice $invoice);
}
