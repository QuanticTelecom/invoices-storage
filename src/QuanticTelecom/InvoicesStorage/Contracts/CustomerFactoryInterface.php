<?php

namespace QuanticTelecom\InvoicesStorage\Contracts;

use QuanticTelecom\Invoices\Contracts\CustomerInterface;

/**
 * Interface CustomerFactoryInterface.
 */
interface CustomerFactoryInterface
{
    /**
     * Build a new CustomerInterface instance.
     *
     * @param string $type type of the customer
     * @param array  $data all data to create a customer
     *
     * @return CustomerInterface
     */
    public function build($type, $data = []);

    /**
     * Transform a customer into an array of data.
     *
     * @param CustomerInterface $customer
     *
     * @return array
     */
    public function toArray(CustomerInterface $customer);
}
