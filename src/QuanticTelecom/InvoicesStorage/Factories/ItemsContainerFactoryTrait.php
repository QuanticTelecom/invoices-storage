<?php namespace QuanticTelecom\InvoicesStorage\Factories;

use QuanticTelecom\Invoices\Contracts\ItemsContainerInterface;
use QuanticTelecom\InvoicesStorage\Exceptions\InvalidDataForItemsContainerFactoryException;

trait ItemsContainerFactoryTrait
{
    /**
     * Fill the items container with all items
     *
     * @param ItemsContainerInterface $itemsContainer
     * @param ItemFactoryInterface $itemFactory
     * @param array $itemsData
     * @throws InvalidDataForItemsContainerFactoryException
     */
    protected function fillItems(
        ItemsContainerInterface $itemsContainer,
        ItemFactoryInterface $itemFactory,
        $itemsData = []
    ) {
        if (!is_array($itemsData)) {
            throw new InvalidDataForItemsContainerFactoryException;
        }

        foreach ($itemsData as $itemData) {
            $this->checkItemData($itemData);
            $item = $itemFactory->build($itemData['type'], $itemData);
            $itemsContainer->addItem($item);
        }
    }

    /**
     * Throw an exception if 'type' key is not present
     *
     * @param array $itemData
     * @return void
     * @throws InvalidDataForItemsContainerFactoryException
     */
    private function checkItemData($itemData = [])
    {
        if (!is_array($itemData) or !array_key_exists('type', $itemData)) {
            throw new InvalidDataForItemsContainerFactoryException;
        }
    }
}
