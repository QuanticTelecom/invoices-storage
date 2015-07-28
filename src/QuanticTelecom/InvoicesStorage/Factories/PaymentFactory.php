<?php namespace QuanticTelecom\InvoicesStorage\Factories;

use QuanticTelecom\Invoices\Contracts\PaymentInterface;
use QuanticTelecom\Invoices\Payment;
use QuanticTelecom\InvoicesStorage\Contracts\PaymentFactoryInterface;

class PaymentFactory implements PaymentFactoryInterface
{
    /**
     * Build a new PaymentInterface instance.
     *
     * @param string $type type of the customer
     * @param array $data all data to create a customer
     *
     * @return PaymentInterface
     */
    public function build($type, $data = [])
    {
        $payment = new Payment();
        $payment->setPaymentName($data['name']);
        $payment->setPaymentDate($data['date']);

        return $payment;
    }

    /**
     * Get the type of payment.
     *
     * @param PaymentInterface $class
     *
     * @return string type of payment
     */
    public function inverseResolution(PaymentInterface $class)
    {
        return 'payment';
    }
}