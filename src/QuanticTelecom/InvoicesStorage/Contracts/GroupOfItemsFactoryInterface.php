<?php namespace QuanticTelecom\InvoicesStorage\Contracts;

use QuanticTelecom\Invoices\Contracts\GroupOfItemsInterface;

/**
 * Interface GroupOfItemsFactoryInterface
 * @package QuanticTelecom\InvoicesStorage\Factories
 */
interface GroupOfItemsFactoryInterface
{
    /**
     * Build a new GroupOfItemsInterface instance.
     *
     * @param string $type type of the group of items
     * @param array $data all data to create a group of items
     * @return GroupOfItemsInterface
     */
    public function build($type, $data = []);

    /**
     * Get the type of the group of items.
     *
     * @param GroupOfItemsInterface $class
     * @return string type of group of items
     */
    public function inverseResolution(GroupOfItemsInterface $class);
}
