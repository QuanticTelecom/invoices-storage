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
     * Get the type of item.
     *
     * @param ItemInterface $class
     *
     * @return string type of item
     */
    public function inverseResolution(ItemInterface $class);
}
