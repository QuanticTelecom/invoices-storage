<?php

namespace QuanticTelecom\InvoicesStorage\Factories;

use QuanticTelecom\Invoices\Contracts\GroupOfItemsInterface;
use QuanticTelecom\Invoices\Contracts\ItemInterface;
use QuanticTelecom\Invoices\GroupOfItems;
use QuanticTelecom\InvoicesStorage\Contracts\GroupOfItemsArrayValidatorInterface;
use QuanticTelecom\InvoicesStorage\Contracts\GroupOfItemsFactoryInterface;
use QuanticTelecom\InvoicesStorage\Contracts\ItemFactoryInterface;
use QuanticTelecom\InvoicesStorage\Exceptions\GroupOfItemsFactory\GroupOfItemsTypeNotFoundException;
use QuanticTelecom\InvoicesStorage\Exceptions\GroupOfItemsFactory\UnknownGroupOfItemsClassException;
use QuanticTelecom\InvoicesStorage\Exceptions\InvalidDataForGroupsContainerFactoryException;

/**
 * Class GroupOfItemsFactory.
 */
class GroupOfItemsFactory implements GroupOfItemsFactoryInterface
{
    use ItemsContainerFactoryTrait;
    use GroupsContainerFactoryTrait;

    /**
     * @var ItemFactoryInterface
     */
    protected $itemFactory;

    /**
     * @var GroupOfItemsArrayValidatorInterface
     */
    protected $groupOfItemsArrayValidator;

    /**
     * @param ItemFactoryInterface                $itemFactory
     * @param GroupOfItemsArrayValidatorInterface $groupOfItemsArrayValidator
     */
    public function __construct(
        ItemFactoryInterface $itemFactory,
        GroupOfItemsArrayValidatorInterface $groupOfItemsArrayValidator
    ) {
        $this->itemFactory = $itemFactory;
        $this->groupOfItemsArrayValidator = $groupOfItemsArrayValidator;
    }

    /**
     * Build a new GroupOfItemsInterface instance.
     *
     * @param string $type type of the group of items
     * @param array  $data all data to create a group of items
     *
     * @return GroupOfItemsInterface
     */
    public function build($type, $data = [])
    {
        if (!$this->groupOfItemsArrayValidator->validate($data)) {
            throw new InvalidDataForGroupsContainerFactoryException();
        }

        $groupOfItems = new GroupOfItems($data['name']);

        if (isset($data['items']) and is_array($data['items'])) {
            $groupOfItems = $this->fillItems($groupOfItems, $this->itemFactory, $data['items']);
        }

        if (isset($data['groups']) and is_array($data['groups'])) {
            $groupOfItems = $this->fillGroups($groupOfItems, $this, $data['groups']);
        }

        return $groupOfItems;
    }

    /**
     * Transform a group of items into an array of data.
     *
     * @param GroupOfItemsInterface $groupOfItem
     *
     * @return array
     */
    public function toArray(GroupOfItemsInterface $groupOfItem)
    {
        return [
            'type' => 'groupOfItems',
            'name' => $groupOfItem->getName(),
            'items' => array_map(function (ItemInterface $item) {
                return $this->itemFactory->toArray($item);
            }, $groupOfItem->getItems())
        ];
    }
}
