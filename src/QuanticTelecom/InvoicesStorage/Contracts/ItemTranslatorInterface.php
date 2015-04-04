<?php namespace QuanticTelecom\InvoicesStorage\Contracts;

use QuanticTelecom\Invoices\Contracts\ItemInterface as ItemLogic;
use QuanticTelecom\InvoicesStorage\Models\Item as ItemModel;

/**
 * Interface ItemTranslatorInterface
 * @package QuanticTelecom\InvoicesStorage\Contracts
 */
interface ItemTranslatorInterface
{
    /**
     * @param ItemModel $itemModel
     * @param ItemLogic $itemLogic | null Item logic instance to use
     * @return ItemLogic
     */
    public function itemModelToItemLogic(
        ItemModel $itemModel,
        ItemLogic $itemLogic = null
    );

    /**
     * @param ItemLogic $itemLogic
     * @return ItemModel
     */
    public function itemLogicToItemModel(ItemLogic $itemLogic);
}
