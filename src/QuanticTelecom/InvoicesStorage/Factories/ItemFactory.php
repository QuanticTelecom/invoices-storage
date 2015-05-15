<?php namespace QuanticTelecom\InvoicesStorage\Factories;

use QuanticTelecom\Invoices\Contracts\ItemInterface;
use QuanticTelecom\Invoices\Item;
use QuanticTelecom\InvoicesStorage\Contracts\ItemFactoryInterface;

/**
 * Class ItemFactory
 * @package QuanticTelecom\InvoicesStorage\Factories
 */
class ItemFactory implements ItemFactoryInterface
{
    /**
     * Build a new ItemInterface instance.
     *
     * @param string $type type of the item
     * @param array $data all data to create an item
     * @return ItemInterface
     */
    public function build($type, $data = [])
    {
        return new Item(
            $data['name'],
            $data['quantity'],
            $data['includingTaxUnitPrice'],
            $data['includingTaxTotalPrice'],
            $data['excludingTaxUnitPrice'],
            $data['excludingTaxTotalPrice']
        );
    }

    /**
     * Get the type of item.
     *
     * @param ItemInterface $class
     * @return string type of item
     */
    public function inverseResolution(ItemInterface $class)
    {
        return "item";
    }
}
