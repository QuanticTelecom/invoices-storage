<?php namespace QuanticTelecom\InvoicesStorage\Factories;

use MongoDate;
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
     * Transform a payment into an array of data.
     *
     * @param PaymentInterface $payment
     *
     * @return array
     */
    public function toArray(PaymentInterface $payment)
    {
        return [
            'type' => 'payment',
            'name' => $payment->getPaymentName(),
            'date' => new MongoDate($payment->getPaymentDate()->timestamp),
        ];
    }
}