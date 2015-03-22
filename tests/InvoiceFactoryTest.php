<?php namespace QuanticTelecom\InvoicesStorage\Tests;

use Mockery as m;
use QuanticTelecom\Invoices\AbstractInvoice;
use QuanticTelecom\Invoices\ExcludingTaxInvoice;
use QuanticTelecom\Invoices\IncludingTaxInvoice;

class InvoiceFactoryTest extends InvoiceStorageTest
{
    /**
     * @test
     */
    public function weGetTheTypeOfInvoice()
    {
        $excludingTaxInvoice = m::mock(ExcludingTaxInvoice::class);
        $includingTaxInvoice = m::mock(IncludingTaxInvoice::class);

        $this->assertEquals($this->invoiceFactory->inverseResolution($excludingTaxInvoice), 'excludingTaxInvoice');
        $this->assertEquals($this->invoiceFactory->inverseResolution($includingTaxInvoice), 'includingTaxInvoice');
    }

    /**
     * @test
     * @expectedException \QuanticTelecom\InvoicesStorage\Exceptions\InvoiceFactory\UnknownInvoiceClassException
     */
    public function itThrowsAnExceptionInCaseOfUnknownClass()
    {
        $unknownClass = m::mock(AbstractInvoice::class);

        $this->invoiceFactory->inverseResolution($unknownClass);
    }

    /**
     * @test
     */
    public function weGetAnIncludingTaxInvoiceWithAllData()
    {
        $data = $this->getFactoryData();

        $invoice = $this->invoiceFactory->build('includingTaxInvoice', $data);

        $this->assertInstanceOf(IncludingTaxInvoice::class, $invoice);

        $this->assertAllDataAreSet($invoice, $data);
    }

    /**
     * @test
     */
    public function weGetAnExcludingTaxInvoiceWithAllData()
    {
        $data = $this->getFactoryData();

        $invoice = $this->invoiceFactory->build('excludingTaxInvoice', $data);

        $this->assertInstanceOf(ExcludingTaxInvoice::class, $invoice);

        $this->assertAllDataAreSet($invoice, $data);
    }

    /**
     * @test
     */
    public function weGetAPaidInvoiceIfPaymentKeyIsSet()
    {
        $data = $this->getFactoryData([
            'payment' => true
        ]);

        $includingTaxInvoice = $this->invoiceFactory->build('includingTaxInvoice', $data);
        $excludingTaxInvoice = $this->invoiceFactory->build('excludingTaxInvoice', $data);

        $this->assertPaymentIsSet($includingTaxInvoice);
        $this->assertPaymentIsSet($excludingTaxInvoice);
    }

    /**
     * @test
     */
    public function weGetAnInvoiceWithItemsIfItemsKeyIsSet()
    {
        $data = $this->getFactoryData([
            'items' => true
        ]);

        $includingTaxInvoice = $this->invoiceFactory->build('includingTaxInvoice', $data);
        $excludingTaxInvoice = $this->invoiceFactory->build('excludingTaxInvoice', $data);

        $this->assertItemsAreSet($includingTaxInvoice, $data['items']);
        $this->assertItemsAreSet($excludingTaxInvoice, $data['items']);
    }

    /**
     * @test
     */
    public function weGetAnInvoiceWithGroupsIfGroupsKeyIsSet()
    {
        $data = $this->getFactoryData([
            'groups' => true
        ]);

        $includingTaxInvoice = $this->invoiceFactory->build('includingTaxInvoice', $data);
        $excludingTaxInvoice = $this->invoiceFactory->build('excludingTaxInvoice', $data);

        $this->assertGroupsAreSet($includingTaxInvoice, $data['groups']);
        $this->assertGroupsAreSet($excludingTaxInvoice, $data['groups']);
    }
}
