<?php namespace QuanticTelecom\InvoicesStorage\Contracts; 

interface GroupOfItemsArrayValidatorInterface {

    /**
     * Check if every necessary data is provided.
     *
     * @param array $data group of items' data
     * @return boolean
     */
    public function validate($data);
}