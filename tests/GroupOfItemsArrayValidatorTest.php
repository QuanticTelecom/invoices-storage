<?php

namespace QuanticTelecom\InvoicesStorage\tests;

use QuanticTelecom\InvoicesStorage\Validators\GroupOfItemsArrayValidator;

class GroupOfItemsArrayValidatorTest extends InvoiceStorageTest
{
    /**
     * @var array
     */
    protected $arrayToValidate;

    /**
     * @var GroupOfItemsArrayValidator
     */
    protected $groupOfItemsArrayValidator;

    public function setUp()
    {
        parent::setUp();

        $this->groupOfItemsArrayValidator = new GroupOfItemsArrayValidator();

        $this->arrayToValidate = [
            'name' => 'test',
        ];
    }

    /**
     * @test
     */
    public function itSucceedsIfAllDataAreProvided()
    {
        $data = $this->arrayToValidate;

        $this->assertTrue(
            $this->groupOfItemsArrayValidator->validate($data)
        );
    }

    /**
     * @test
     */
    public function itFailsIfThereIsNoNameKey()
    {
        $data = $this->arrayToValidate;
        unset($data['name']);

        $this->assertFalse(
            $this->groupOfItemsArrayValidator->validate($data)
        );
    }
}
