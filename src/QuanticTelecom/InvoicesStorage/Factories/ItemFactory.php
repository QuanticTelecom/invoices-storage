<?php

namespace QuanticTelecom\InvoicesStorage\Factories;

use QuanticTelecom\Invoices\Contracts\ItemInterface;
use QuanticTelecom\Invoices\Item;
use QuanticTelecom\InvoicesStorage\Contracts\ItemFactoryInterface;

/**
 * Class ItemFactory.
 */
class ItemFactory implements ItemFactoryInterface
{
    /**
     * Build a new ItemInterface instance.
     *
     * @param string $type type of the item
     * @param array  $data all data to create an item
     *
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
     * Transform an item into an array of data.
     *
     * @param ItemInterface $item
     *
     * @return array
     */
    public function toArray(ItemInterface $item)
    {
        return [
            'type' => 'item',
            'name' => $item->getItemName(),
            'quantity' => $item->getItemQuantity(),
            'includingTaxUnitPrice' => $item->getItemIncludingTaxUnitPrice(),
            'includingTaxTotalPrice' => $item->getItemIncludingTaxTotalPrice(),
            'excludingTaxUnitPrice' => $item->getItemExcludingTaxUnitPrice(),
            'excludingTaxTotalPrice' => $item->getItemExcludingTaxTotalPrice(),
        ];
    }
}
