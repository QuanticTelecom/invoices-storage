<?php namespace QuanticTelecom\InvoicesStorage\Contracts;

use QuanticTelecom\InvoicesStorage\Models\Invoice as InvoiceModel;
use QuanticTelecom\Invoices\AbstractInvoice as InvoiceLogic;

/**
 * Interface InvoiceTranslatorInterface
 * @package QuanticTelecom\InvoicesStorage\Contracts
 */
interface InvoiceTranslatorInterface
{
    /**
     * @param InvoiceModel $invoiceModel
     * @return InvoiceLogic
     */
    public function invoiceModelToInvoiceLogic(InvoiceModel $invoiceModel);

    /**
     * @param InvoiceLogic $invoiceLogic
     * @return InvoiceModel
     */
    public function invoiceLogicToInvoiceModel(InvoiceLogic $invoiceLogic);
}
