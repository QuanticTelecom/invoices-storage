<?php namespace QuanticTelecom\InvoicesStorage\Factories;

use QuanticTelecom\Invoices\Contracts\GroupsContainerInterface;
use QuanticTelecom\InvoicesStorage\Exceptions\InvalidDataForGroupsContainerFactoryException;

/**
 * Class GroupsContainerFactoryTrait
 * @package QuanticTelecom\InvoicesStorage\Factories
 */
trait GroupsContainerFactoryTrait
{
    /**
     * Fill the items container with all items
     *
     * @param GroupsContainerInterface $groupsContainer
     * @param GroupOfItemsFactoryInterface $groupOfItemsFactory
     * @param array $groupOfItemsData
     * @throws InvalidDataForGroupsContainerFactoryException
     */
    protected function fillGroups(
        GroupsContainerInterface $groupsContainer,
        GroupOfItemsFactoryInterface $groupOfItemsFactory,
        $groupsOfItemsData = []
    ) {
        if (!is_array($groupsOfItemsData)) {
            throw new InvalidDataForGroupsContainerFactoryException;
        }

        foreach ($groupsOfItemsData as $groupOfItemsData) {
            $this->checkGroupOfItemsData($groupOfItemsData);
            $groupOfItems = $groupOfItemsFactory->build($groupOfItemsData['type'], $groupOfItemsData);
            $groupsContainer->addGroup($groupOfItems);
        }
    }

    /**
     * Throw an exception if 'type' key is not present
     *
     * @param array $groupOfItemsData
     * @return void
     * @throws InvalidDataForGroupsContainerFactoryException
     */
    private function checkGroupOfItemsData($groupOfItemsData = [])
    {
        if (!is_array($groupOfItemsData) or !array_key_exists('type', $groupOfItemsData)) {
            throw new InvalidDataForGroupsContainerFactoryException;
        }
    }
}
