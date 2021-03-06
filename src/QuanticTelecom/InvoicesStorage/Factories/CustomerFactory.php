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
     * Transform a customer into an array of data.
     *
     * @param CustomerInterface $customer
     *
     * @return array
     */
    public function toArray(CustomerInterface $customer)
    {
        return [
            'type' => 'customer',
            'id' => $customer->getCustomerId(),
            'name' => $customer->getCustomerName(),
            'address' => $customer->getCustomerAddress(),
        ];
    }
}
