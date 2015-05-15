<?php

namespace QuanticTelecom\InvoicesStorage\Contracts;

use Carbon\Carbon;
use QuanticTelecom\Invoices\AbstractInvoice;

interface LastInvoiceRepositoryInterface
{
    /**
     * Get the last invoice saved for the month provided by the Carbon date.
     *
     * @param Carbon $date
     *
     * @return AbstractInvoice|null the last invoice for the provided date or null if there is no invoice
     */
    public function getLastInvoiceForMonth(Carbon $date);
}
