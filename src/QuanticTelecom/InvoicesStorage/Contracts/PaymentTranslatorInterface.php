<?php namespace QuanticTelecom\InvoicesStorage\Contracts;

use QuanticTelecom\Invoices\Contracts\PaymentInterface as PaymentLogic;
use QuanticTelecom\InvoicesStorage\Models\Payment as PaymentModel;

interface PaymentTranslatorInterface
{
    /**
     * @param PaymentModel $paymentModel
     * @param PaymentLogic $paymentLogic | null Payment logic instance to use
     * @return PaymentLogic
     */
    public function paymentModelToPaymentLogic(
        PaymentModel $paymentModel,
        PaymentLogic $paymentLogic = null
    );

    /**
     * @param PaymentLogic $paymentLogic
     * @return PaymentModel
     */
    public function paymentLogicToPaymentModel(PaymentLogic $paymentLogic);
}
