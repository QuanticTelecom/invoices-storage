<?php

namespace QuanticTelecom\InvoicesStorage\Contracts;

use QuanticTelecom\Invoices\Contracts\InvoiceInterface;
use QuanticTelecom\InvoicesStorage\Exceptions\InvoiceFactory\InvalidDataForInvoiceFactoryException;
use QuanticTelecom\InvoicesStorage\Exceptions\InvoiceFactory\InvoiceTypeNotFoundException;

/**
 * Interface InvoiceFactoryInterface.
 */
interface InvoiceFactoryInterface
{
    /**
     * Build a new InvoiceInterface instance.
     *
     * @param string $type type of invoice
     * @param array  $data all data to create an invoice
     *
     * @return InvoiceInterface
     *
     * @throws InvalidDataForInvoiceFactoryException|InvoiceTypeNotFoundException
     */
    public function build($type, $data = []);

    /**
     * Transform an invoice into an array of data.
     *
     * @param InvoiceInterface $invoice
     *
     * @return array
     */
    public function toArray(InvoiceInterface $invoice);
}
