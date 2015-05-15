<?php

namespace QuanticTelecom\InvoicesStorage\Contracts;

use QuanticTelecom\Invoices\AbstractInvoice;
use QuanticTelecom\Invoices\Contracts\CustomerInterface;

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

    /**
     * Get all invoices for a Customer.
     *
     * @param CustomerInterface $customer
     *
     * @return AbstractInvoice[]
     */
    public function getAllByCustomer(CustomerInterface $customer);
}
