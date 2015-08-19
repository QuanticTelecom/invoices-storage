<?php

namespace QuanticTelecom\InvoicesStorage\Contracts;

use QuanticTelecom\Invoices\Contracts\ItemInterface;

/**
 * Interface ItemFactoryInterface.
 */
interface ItemFactoryInterface
{
    /**
     * Build a new ItemInterface instance.
     *
     * @param string $type type of the item
     * @param array  $data all data to create an item
     *
     * @return ItemInterface
     */
    public function build($type, $data = []);

    /**
     * Transform an item into an array of data.
     *
     * @param ItemInterface $item
     *
     * @return array
     */
    public function toArray(ItemInterface $item);
}
