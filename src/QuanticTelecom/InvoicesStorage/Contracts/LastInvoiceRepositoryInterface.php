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
     * @return AbstractInvoice
     */
    public function getLastInvoiceForMonth(Carbon $date);
}
