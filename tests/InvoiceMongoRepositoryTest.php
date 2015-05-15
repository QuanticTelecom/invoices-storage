<?php

namespace QuanticTelecom\InvoicesStorage\tests;

use MongoClient;
use Mockery as m;
use QuanticTelecom\Invoices\Contracts\CustomerInterface;
use QuanticTelecom\Invoices\Contracts\PaymentInterface;
use QuanticTelecom\Invoices\IncludingTaxInvoice;
use QuanticTelecom\InvoicesStorage\Contracts\CustomerFactoryInterface;
use QuanticTelecom\InvoicesStorage\Contracts\GroupOfItemsArrayValidatorInterface;
use QuanticTelecom\InvoicesStorage\Contracts\InvoiceArrayValidatorInterface;
use QuanticTelecom\InvoicesStorage\Contracts\PaymentFactoryInterface;
use QuanticTelecom\InvoicesStorage\Factories\GroupOfItemsFactory;
use QuanticTelecom\InvoicesStorage\Factories\InvoiceFactory;
use QuanticTelecom\InvoicesStorage\Factories\ItemFactory;
use QuanticTelecom\InvoicesStorage\Repositories\InvoiceMongoRepository;

class InvoiceMongoRepositoryTest extends InvoiceStorageTest
{
    /**
     * @var InvoiceMongoRepository
     */
    protected $repository;

    public function setUp()
    {
        parent::setUp();
        $mongoClient = new MongoClient();
        $mongoClient->test->drop();

        $invoiceArrayValidator = m::mock(InvoiceArrayValidatorInterface::class);
        $invoiceArrayValidator->shouldReceive('validate')->andReturn(true);

        $groupOfItemsArrayValidator = m::mock(GroupOfItemsArrayValidatorInterface::class);
        $groupOfItemsArrayValidator->shouldReceive('validate')->andReturn(true);

        $customerFactory = m::mock(CustomerFactoryInterface::class);
        $customerFactory->shouldReceive('inverseResolution')->andReturn('customer');
        $customerFactory->shouldReceive('build')->andReturn(m::mock(CustomerInterface::class));

        $paymentFactory = m::mock(PaymentFactoryInterface::class);
        $paymentFactory->shouldReceive('inverseResolution')->andReturn('payment');
        $paymentFactory->shouldReceive('build')->andReturn(m::mock(PaymentInterface::class));

        $itemFactory = new ItemFactory();

        $groupOfItemsFactory = new GroupOfItemsFactory($itemFactory, $groupOfItemsArrayValidator);

        $invoiceFactory = new InvoiceFactory(
            $customerFactory,
            $paymentFactory,
            $itemFactory,
            $groupOfItemsFactory,
            $invoiceArrayValidator
        );

        $this->repository = new InvoiceMongoRepository(
            $mongoClient->test,
            $invoiceFactory,
            $customerFactory,
            $paymentFactory,
            $itemFactory,
            $groupOfItemsFactory
        );
    }

    /**
     * @test
     */
    public function weSaveAnInvoice()
    {
        $invoice = $this->getNewInvoice(IncludingTaxInvoice::class);
        $this->repository->save($invoice);

        $invoiceSaved = $this->repository->get($invoice->getId());

        $this->assertEquals($invoice->getId(), $invoiceSaved->getId());
        $this->assertEquals($invoice->getCreatedAt(), $invoiceSaved->getCreatedAt());
        $this->assertEquals($invoice->getDueDate(), $invoiceSaved->getDueDate());
    }
}
