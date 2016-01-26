<?php

namespace QuanticTelecom\InvoicesStorage\Tests;

use ArrayIterator;
use Mockery\MockInterface;
use Mockery as m;
use MongoDB\Collection;
use PHPUnit_Framework_TestCase;
use QuanticTelecom\Invoices\Contracts\CustomerInterface;
use QuanticTelecom\InvoicesStorage\Contracts\InvoiceFactoryInterface;
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

        $this->collection = m::mock(Collection::class);
        $this->invoiceFactory = m::mock(InvoiceFactoryInterface::class);

        $this->repository = new InvoiceMongoRepository(
            $this->collection,
            $this->invoiceFactory
        );
    }

    public function tearDown()
    {
        m::close();
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
        $invoices = $this->getNewCursor();
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

        $invoices = $this->getNewCursor();
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
     * Get a new Cursor with two elements.
     *
     * @return ArrayIterator
     */
    private function getNewCursor()
    {
        return new ArrayIterator([[
            'type' => 'fake',
            'some-other-key' => true
        ], [
            'type' => 'real',
            'some-other-key' => false
        ]]);
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
