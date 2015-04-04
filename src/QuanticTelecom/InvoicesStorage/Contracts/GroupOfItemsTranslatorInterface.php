<?php namespace QuanticTelecom\InvoicesStorage\Contracts;

use QuanticTelecom\Invoices\GroupOfItems as GroupOfItemsLogic;
use QuanticTelecom\InvoicesStorage\Models\GroupOfItems as GroupOfItemsModel;

/**
 * Interface GroupOfItemsTranslatorInterface
 * @package QuanticTelecom\InvoicesStorage\Contracts
 */
interface GroupOfItemsTranslatorInterface
{
    /**
     * @param GroupOfItemsModel $groupOfItemsModel
     * @param GroupOfItemsLogic $groupOfItemsLogic | null GroupOfItems logic instance to use
     * @return GroupOfItemsLogic
     */
    public function groupOfItemsModelToGroupOfItemsLogic(
        GroupOfItemsModel $groupOfItemsModel,
        GroupOfItemsLogic $groupOfItemsLogic = null
    );

    /**
     * @param GroupOfItemsLogic $groupOfItemsLogic
     * @return GroupOfItemsModel
     */
    public function groupOfItemsLogicToGroupOfItemsModel(GroupOfItemsLogic $groupOfItemsLogic);
}
