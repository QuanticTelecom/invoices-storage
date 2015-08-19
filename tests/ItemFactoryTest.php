<?php

namespace QuanticTelecom\InvoicesStorage\Tests;

use QuanticTelecom\Invoices\Contracts\ItemInterface;
use QuanticTelecom\Invoices\Item;
use QuanticTelecom\InvoicesStorage\Contracts\ItemFactoryInterface;
use QuanticTelecom\InvoicesStorage\Factories\ItemFactory;

class ItemFactoryTest extends InvoiceStorageTest
{
    /**
     * @var ItemFactoryInterface
     */
    protected $itemFactory;

    /**
     * @var ItemInterface
     */
    protected $item;

    /**
     * @var array
     */
    protected $itemArray;

    public function setUp()
    {
        parent::setUp();

        $this->itemFactory = new ItemFactory();

        // Get a fake item from helper
        $this->itemArray = $this->itemsData['ring'];
        $this->itemArray['type'] = 'item';
        $this->item = new Item(
            $this->itemArray['name'],
            $this->itemArray['quantity'],
            $this->itemArray['includingTaxUnitPrice'],
            $this->itemArray['includingTaxTotalPrice'],
            $this->itemArray['excludingTaxUnitPrice'],
            $this->itemArray['excludingTaxTotalPrice']
        );
    }

    /**
     * @test
     */
    public function itBuildAnItemFromAnArrayOfData()
    {
        $expectedItem = $this->item;
        $itemBuilt = $this->itemFactory->build('item', $this->itemArray);

        $this->assertEquals($expectedItem, $itemBuilt);
    }

    /**
     * @test
     */
    public function itTransformsAnItemInArray()
    {
        $expectedArray = $this->itemArray;
        $arrayBuilt = $this->itemFactory->toArray($this->item);

        $this->assertEquals($expectedArray, $arrayBuilt);
    }
}
