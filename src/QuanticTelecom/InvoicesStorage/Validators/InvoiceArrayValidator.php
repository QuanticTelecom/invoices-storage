<?php

namespace QuanticTelecom\InvoicesStorage\Validators;

use QuanticTelecom\InvoicesStorage\Contracts\InvoiceArrayValidatorInterface;

class InvoiceArrayValidator implements InvoiceArrayValidatorInterface
{
    /**
     * Check if every necessary data is provided.
     *
     * @param array $data invoice's data
     *
     * @return bool
     */
    public function validate($data)
    {
        return  array_key_exists('id', $data)
            and array_key_exists('customer', $data)
            and is_array($data['customer'])
            and array_key_exists('type', $data['customer']);
    }
}
