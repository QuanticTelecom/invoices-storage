<?php namespace QuanticTelecom\InvoicesStorage\Contracts;

use QuanticTelecom\Invoices\Contracts\PaymentInterface;

/**
 * Interface PaymentFactoryInterface
 * @package QuanticTelecom\InvoicesStorage\Factories
 */
interface PaymentFactoryInterface
{
    /**
     * Build a new PaymentInterface instance.
     *
     * @param string $type type of the customer
     * @param array $data all data to create a customer
     * @return PaymentInterface
     */
    public function build($type, $data = []);

    /**
     * Get the type of payment.
     *
     * @param PaymentInterface $class
     * @return string type of payment
     */
    public function inverseResolution(PaymentInterface $class);
}
