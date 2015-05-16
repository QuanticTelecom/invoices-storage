<?php

namespace QuanticTelecom\InvoicesStorage\Factories;

use QuanticTelecom\Invoices\Contracts\CustomerInterface;
use QuanticTelecom\Invoices\Customer;
use QuanticTelecom\InvoicesStorage\Contracts\CustomerFactoryInterface;

class CustomerFactory implements CustomerFactoryInterface
{
    /**
     * Build a new CustomerInterface instance.
     *
     * @param string $type type of the customer
     * @param array  $data all data to create a customer
     *
     * @return CustomerInterface
     */
    public function build($type, $data = [])
    {
        $customer = new Customer();
        $customer->setCustomerId($data['id']);
        $customer->setCustomerName($data['name']);
        $customer->setCustomerAddress($data['address']);

        return $customer;
    }

    /**
     * Get the type of customer.
     *
     * @param CustomerInterface $class
     *
     * @return string type of customer
     */
    public function inverseResolution(CustomerInterface $class)
    {
        return 'customer';
    }
}
