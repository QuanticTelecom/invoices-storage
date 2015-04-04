<?php namespace QuanticTelecom\InvoicesStorage\Contracts;

use QuanticTelecom\Invoices\Contracts\CustomerInterface as CustomerLogic;
use QuanticTelecom\InvoicesStorage\Models\Customer as CustomerModel;

/**
 * Interface CustomerTranslatorInterface
 * @package QuanticTelecom\InvoicesStorage\Contracts
 */
interface CustomerTranslatorInterface
{
    /**
     * @param CustomerModel $customerModel
     * @param CustomerLogic $customerLogic | null Customer logic instance to use
     * @return CustomerLogic
     */
    public function customerModelToCustomerLogic(
        CustomerModel $customerModel,
        CustomerLogic $customerLogic = null
    );

    /**
     * @param CustomerLogic $customerLogic
     * @return CustomerModel
     */
    public function customerLogicToCustomerModel(CustomerLogic $customerLogic);
}
