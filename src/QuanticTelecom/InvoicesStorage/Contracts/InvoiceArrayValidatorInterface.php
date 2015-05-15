<?php namespace QuanticTelecom\InvoicesStorage\Contracts;

interface InvoiceArrayValidatorInterface {

    /**
     * Check if every necessary data is provided.
     *
     * @param array $data invoice's data
     * @return boolean
     */
    public function validate($data);
}