<?php

namespace QuanticTelecom\InvoicesStorage\Validators;

use QuanticTelecom\InvoicesStorage\Contracts\GroupOfItemsArrayValidatorInterface;

class GroupOfItemsArrayValidator implements GroupOfItemsArrayValidatorInterface
{
    /**
     * Check if every necessary data is provided.
     *
     * @param array $data group of items' data
     *
     * @return bool
     */
    public function validate($data)
    {
        return array_key_exists('name', $data);
    }
}
