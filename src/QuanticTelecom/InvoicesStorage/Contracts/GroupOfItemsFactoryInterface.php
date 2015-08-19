<?php

namespace QuanticTelecom\InvoicesStorage\Contracts;

use QuanticTelecom\Invoices\Contracts\GroupOfItemsInterface;

/**
 * Interface GroupOfItemsFactoryInterface.
 */
interface GroupOfItemsFactoryInterface
{
    /**
     * Build a new GroupOfItemsInterface instance.
     *
     * @param string $type type of the group of items
     * @param array  $data all data to create a group of items
     *
     * @return GroupOfItemsInterface
     */
    public function build($type, $data = []);

    /**
     * Transform a group of items into an array of data.
     *
     * @param GroupOfItemsInterface $groupOfItem
     *
     * @return array
     */
    public function toArray(GroupOfItemsInterface $groupOfItem);
}
