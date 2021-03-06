<?php

namespace QuanticTelecom\InvoicesStorage;

use QuanticTelecom\Invoices\Contracts\IdGeneratorInterface;

/**
 * Class IdGenerator.
 */
class IdGenerator implements IdGeneratorInterface
{
    /**
     * @var
     */
    private $id;

    /**
     * @param $id int ID to generate
     */
    public function __construct($id)
    {
        $this->id = $id;
    }

    /**
     * Return a new ID to use for an invoice.
     *
     * @return string
     */
    public function generateNewId()
    {
        return $this->id;
    }
}
