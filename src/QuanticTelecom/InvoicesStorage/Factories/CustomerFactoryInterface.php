<?php namespace QuanticTelecom\InvoicesStorage\Factories;

use QuanticTelecom\Invoices\Contracts\CustomerInterface;

interface CustomerFactoryInterface
{
    /**
     * Build a new CustomerInterface instance.
     *
     * @param string $type type of the customer
     * @param array $data all data to create a customer
     * @return CustomerInterface
     */
    public function build($type, $data = []);

    /**
     * Get the type of customer.
     *
     * @param CustomerInterface $class
     * @return string type of customer
     */
    public function inverseResolution(CustomerInterface $class);
}