<?php

namespace QuanticTelecom\InvoicesStorage\Factories;

use QuanticTelecom\Invoices\Contracts\GroupOfItemsInterface;
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
        switch ($type) {
            case 'groupOfItems':
                return $this->buildGroupOfItems($data);
                break;
            default:
                throw new GroupOfItemsTypeNotFoundException();
        }
    }

    /**
     * Get the type of the group of items.
     *
     * @param GroupOfItemsInterface $class
     *
     * @return string type of group of items
     */
    public function inverseResolution(GroupOfItemsInterface $class)
    {
        if ($class instanceof GroupOfItems) {
            return 'groupOfItems';
        } else {
            throw new UnknownGroupOfItemsClassException();
        }
    }

    /**
     * Build a new GroupOfItems implementation instance.
     *
     * @param array $data
     *
     * @return GroupOfItems
     */
    protected function buildGroupOfItems($data = [])
    {
        if (!$this->groupOfItemsArrayValidator->validate($data)) {
            throw new InvalidDataForGroupsContainerFactoryException();
        }

        $groupOfItems = new GroupOfItems($data['name']);

        if (isset($data['items']) and is_array($data['items'])) {
            $this->fillItems($groupOfItems, $this->itemFactory, $data['items']);
        }

        if (isset($data['groups']) and is_array($data['groups'])) {
            $this->fillGroups($groupOfItems, $this, $data['groups']);
        }

        return $groupOfItems;
    }
}
