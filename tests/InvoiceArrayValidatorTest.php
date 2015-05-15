<?php

namespace QuanticTelecom\InvoicesStorage\Tests;

use QuanticTelecom\InvoicesStorage\Validators\InvoiceArrayValidator;

class InvoiceArrayValidatorTest extends InvoiceStorageTest
{
    /**
     * @var array
     */
    protected $arrayToValidate;

    public function setUp()
    {
        parent::setUp();

        $this->invoiceArrayValidator = new InvoiceArrayValidator();

        $this->arrayToValidate = [
            'id' => '42',
            'customer' => [
                'type' => 'student',
            ],
        ];
    }

    /**
     * @test
     */
    public function itSucceedsIfAllDataAreProvided()
    {
        $data = $this->arrayToValidate;

        $this->assertTrue(
            $this->invoiceArrayValidator->validate($data)
        );
    }

    /**
     * @test
     */
    public function itFailsIfThereIsNoIdKey()
    {
        $data = $this->arrayToValidate;
        unset($data['id']);

        $this->assertFalse(
            $this->invoiceArrayValidator->validate($data)
        );
    }

    /**
     * @test
     */
    public function itFailsIfThereIsNoCustomerKey()
    {
        $data = $this->arrayToValidate;
        unset($data['customer']);

        $this->assertFalse(
            $this->invoiceArrayValidator->validate($data)
        );
    }

    /**
     * @test
     */
    public function itFailsIfCustomerValueIsNotAnArray()
    {
        $data = $this->arrayToValidate;
        $data['customer'] = 'notAnArray';

        $this->assertFalse(
            $this->invoiceArrayValidator->validate($data)
        );
    }

    /**
     * @test
     */
    public function itFailsIfThereIsNoTypeKeyIntoTheCustomerData()
    {
        $data = $this->arrayToValidate;
        unset($data['customer']['type']);

        $this->assertFalse(
            $this->invoiceArrayValidator->validate($data)
        );
    }
}
