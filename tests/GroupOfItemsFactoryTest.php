<?php

namespace QuanticTelecom\InvoicesStorage\Tests;

use QuanticTelecom\Invoices\Contracts\ItemInterface;
use QuanticTelecom\Invoices\GroupOfItems;
use QuanticTelecom\Invoices\Item;
use QuanticTelecom\InvoicesStorage\Contracts\GroupOfItemsFactoryInterface;
use QuanticTelecom\InvoicesStorage\Contracts\ItemFactoryInterface;
use QuanticTelecom\InvoicesStorage\Factories\GroupOfItemsFactory;
use QuanticTelecom\InvoicesStorage\Factories\ItemFactory;
use QuanticTelecom\InvoicesStorage\Validators\GroupOfItemsArrayValidator;

class GroupOfItemsFactoryTest extends InvoiceStorageTest
{
    /**
     * @var GroupOfItemsFactoryInterface
     */
    protected $groupOfItemsFactory;

    /**
     * @var GroupOfItems
     */
    protected $groupOfItems;

    /**
     * @var array
     */
    protected $groupOfItemsArray;

    public function setUp()
    {
        parent::setUp();

        $this->groupOfItemsFactory = new GroupOfItemsFactory(
            new ItemFactory(),
            new GroupOfItemsArrayValidator()
        );

        // Get a fake item from helper
        $this->groupOfItemsArray = $this->groupsData['stuff'];
        $this->groupOfItemsArray['type'] = 'groupOfItems';
        $this->groupOfItems = new GroupOfItems(
            $this->groupOfItemsArray['name']
        );

        $itemFactory = new ItemFactory();
        $this->groupOfItems->addItem(
            $itemFactory->build('item', $this->groupOfItemsArray['items']['gloves'])
        );
        $this->groupOfItems->addItem(
            $itemFactory->build('item', $this->groupOfItemsArray['items']['armor'])
        );

        // Clear a little bit the array to match an DB item array
        $this->groupOfItemsArray['items']['gloves']['type'] = 'item';
        $this->groupOfItemsArray['items']['armor']['type'] = 'item';
        $this->groupOfItemsArray['items'] = array_values($this->groupOfItemsArray['items']);
    }

    /**
     * @test
     */
    public function itBuildAGroupOfItemsFromAnArrayOfData()
    {
        $expectedGroupOfItems = $this->groupOfItems;
        $groupOfItemsBuilt = $this->groupOfItemsFactory->build(
            'groupOfItems',
            $this->groupOfItemsArray
        );

        $this->assertEquals($expectedGroupOfItems, $groupOfItemsBuilt);
    }

    /**
     * @test
     */
    public function itTransformsAGroupOfItemsInArray()
    {
        $expectedArray = $this->groupOfItemsArray;
        $arrayBuilt = $this->groupOfItemsFactory->toArray($this->groupOfItems);

        $this->assertEquals($expectedArray, $arrayBuilt);
    }
}
