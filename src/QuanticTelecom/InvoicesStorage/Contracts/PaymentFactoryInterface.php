<?php

namespace QuanticTelecom\InvoicesStorage\Contracts;

use QuanticTelecom\Invoices\Contracts\PaymentInterface;

/**
 * Interface PaymentFactoryInterface.
 */
interface PaymentFactoryInterface
{
    /**
     * Build a new PaymentInterface instance.
     *
     * @param string $type type of the customer
     * @param array  $data all data to create a customer
     *
     * @return PaymentInterface
     */
    public function build($type, $data = []);

    /**
     * Transform a payment into an array of data.
     *
     * @param PaymentInterface $payment
     *
     * @return array
     */
    public function toArray(PaymentInterface $payment);
}
