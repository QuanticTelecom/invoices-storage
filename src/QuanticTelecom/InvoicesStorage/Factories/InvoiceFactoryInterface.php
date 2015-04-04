<?php namespace QuanticTelecom\InvoicesStorage\Factories;

use QuanticTelecom\Invoices\Contracts\InvoiceInterface;
use QuanticTelecom\InvoicesStorage\Exceptions\InvoiceFactory\InvalidDataForInvoiceFactoryException;
use QuanticTelecom\InvoicesStorage\Exceptions\InvoiceFactory\InvoiceTypeNotFoundException;
use QuanticTelecom\InvoicesStorage\Exceptions\InvoiceFactory\UnknownInvoiceClassException;

/**
 * Interface InvoiceFactoryInterface
 * @package QuanticTelecom\InvoicesStorage\Factories
 */
interface InvoiceFactoryInterface
{
    /**
     * Build a new InvoiceInterface instance.
     *
     * @param string $type type of invoice
     * @param array $data all data to create an invoice
     * @return InvoiceInterface
     * @throws InvalidDataForInvoiceFactoryException|InvoiceTypeNotFoundException
     */
    public function build($type, $data = []);

    /**
     * Get the type of invoice.
     *
     * @param InvoiceInterface $class
     * @return string type of invoice
     * @throws UnknownInvoiceClassException
     */
    public function inverseResolution(InvoiceInterface $class);
}
