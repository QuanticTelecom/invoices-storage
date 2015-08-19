<?php

namespace QuanticTelecom\InvoicesStorage\Tests;

use Carbon\Carbon;
use Mockery\MockInterface;
use MongoClient;
use Mockery as m;
use MongoCollection;
use MongoCursor;
use PHPUnit_Framework_TestCase;
use QuanticTelecom\Invoices\AbstractInvoice;
use QuanticTelecom\Invoices\Contracts\CustomerInterface;
use QuanticTelecom\Invoices\Contracts\PaymentInterface;
use QuanticTelecom\Invoices\IncludingTaxInvoice;
use QuanticTelecom\InvoicesStorage\Contracts\CustomerFactoryInterface;
use QuanticTelecom\InvoicesStorage\Contracts\GroupOfItemsArrayValidatorInterface;
use QuanticTelecom\InvoicesStorage\Contracts\GroupOfItemsFactoryInterface;
use QuanticTelecom\InvoicesStorage\Contracts\InvoiceArrayValidatorInterface;
use QuanticTelecom\InvoicesStorage\Contracts\InvoiceFactoryInterface;
use QuanticTelecom\InvoicesStorage\Contracts\ItemFactoryInterface;
use QuanticTelecom\InvoicesStorage\Contracts\PaymentFactoryInterface;
use QuanticTelecom\InvoicesStorage\Factories\GroupOfItemsFactory;
use QuanticTelecom\InvoicesStorage\Factories\InvoiceFactory;
use QuanticTelecom\InvoicesStorage\Factories\ItemFactory;
use QuanticTelecom\InvoicesStorage\Repositories\InvoiceMongoRepository;

class InvoiceMongoRepositoryTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var InvoiceMongoRepository
     */
    protected $repository;

    /**
     * @var MockInterface
     */
    private $collection;

    /**
     * @var MockInterface
     */
    private $invoiceFactory;

    public function setUp()
    {
        parent::setUp();

        $this->collection = m::mock(MongoCollection::class);
        $this->invoiceFactory = m::mock(InvoiceFactoryInterface::class);

        $this->repository = new InvoiceMongoRepository(
            $this->collection,
            $this->invoiceFactory
        );
    }

    /**
     * @test
     */
    public function itGetsAnInvoiceById()
    {
        $id = 'random-id';
        $type = 'fake';
        $data = [
            'type' => $type,
            'some-other-key' => true
        ];
        $this->collection
            ->shouldReceive('findOne')
            ->with(['id' => $id])
            ->andReturn($data)
            ->once();

        $this->invoiceFactory
            ->shouldReceive('build')
            ->with($type, $data)
            ->once();

        $this->repository->get($id);
    }

    /**
     * @test
     */
    public function itGetsAllInvoices()
    {
        $invoices = $this->getNewMongoCursor();
        $this->collection
            ->shouldReceive('find')
            ->withNoArgs()
            ->andReturn($invoices)
            ->once();

        $this->invoiceFactory
            ->shouldReceive('build')
            ->twice();

        $this->repository->getAll();
    }

    /**
     * @test
     */
    public function itGetsTheInvoicesForACustomer()
    {
        $customerId = 'sauron';
        $customer = m::mock(CustomerInterface::class);
        $customer->shouldReceive('getCustomerId')
            ->once()
            ->andReturn($customerId);

        $invoices = $this->getNewMongoCursor();
        $this->collection
            ->shouldReceive('find')
            ->once()
            ->with([
                'customer.id' => $customerId
            ])
            ->andReturn($invoices);

        $this->invoiceFactory
            ->shouldReceive('build')
            ->twice();

        $this->repository->getAllByCustomer($customer);
    }

    /**
     * Get a new MongoCursor with two elements.
     *
     * @return MockInterface
     */
    private function getNewMongoCursor()
    {
        $cursor = m::mock(MongoCursor::class);
        $cursor->shouldReceive('rewind');
        $cursor->shouldReceive('valid')->andReturn(true, true, false);
        $cursor->shouldReceive('current')->andReturn([
            'type' => 'fake',
            'some-other-key' => true
        ], [
            'type' => 'real',
            'some-other-key' => false
        ]);
        $cursor->shouldReceive('key')->andReturn('first-key', 'second-key');
        $cursor->shouldReceive('next');

        return $cursor;
    }

    /**
     * @test
     */
    public function weGetTheLastInvoice()
    {
        // TODO: check the last invoice method.
    }

    /**
     * @test
     */
    public function weGetNullWhenThereIsNoInvoice()
    {
        // TODO: check the last invoice method.
    }
}
