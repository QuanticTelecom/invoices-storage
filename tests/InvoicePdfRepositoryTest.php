<?php

namespace QuanticTelecom\InvoicesStorage\Tests;

use Illuminate\Filesystem\Filesystem;
use Mockery as m;
use QuanticTelecom\Invoices\Contracts\CustomerInterface;
use QuanticTelecom\Invoices\Contracts\InvoiceInterface;
use QuanticTelecom\Invoices\Contracts\PdfGeneratorInterface;
use QuanticTelecom\Invoices\IncludingTaxInvoice;
use QuanticTelecom\InvoicesStorage\Contracts\InvoiceRepositoryInterface;
use QuanticTelecom\InvoicesStorage\Contracts\PdfStorageInterface;
use QuanticTelecom\InvoicesStorage\Repositories\InvoicePdfRepository;

class InvoicePdfRepositoryTest extends InvoiceStorageTest
{
    /**
     * @var InvoicePdfRepository
     */
    protected $repository;

    /**
     * @var m\MockInterface
     */
    protected $invoiceRepository;

    /**
     * @var m\MockInterface
     */
    protected $filesystem;

    /**
     * @var m\MockInterface
     */
    protected $pdfGenerator;

    /**
     * @var m\MockInterface
     */
    protected $pdfStorage;

    public function setUp()
    {
        parent::setUp();

        $this->invoiceRepository = m::mock(InvoiceRepositoryInterface::class);
        $this->filesystem = m::mock(Filesystem::class);
        $this->pdfGenerator = m::mock(PdfGeneratorInterface::class);
        $this->pdfStorage = m::mock(PdfStorageInterface::class);

        $this->repository = new InvoicePdfRepository(
            $this->invoiceRepository,
            $this->filesystem,
            $this->pdfGenerator,
            $this->pdfStorage
        );
    }

    /**
     * @test
     */
    public function itSavesGeneratesAndPuts()
    {
        $invoice = $this->getNewInvoice(IncludingTaxInvoice::class);
        $storagePath = '/tmp/mockInvoice.pdf';
        $pdfString = 'this is the PDF string';

        $this->invoiceRepository->shouldReceive('save')->with($invoice)->once();
        $this->pdfGenerator->shouldReceive('generate')
            ->with($invoice)
            ->once()
            ->andReturn($pdfString);
        $this->pdfStorage->shouldReceive('getStoragePath')
            ->with($invoice)
            ->once()
            ->andReturn($storagePath);
        $this->filesystem->shouldReceive('put')
            ->with($storagePath, $pdfString)
            ->once();

        $this->repository->save($invoice);
    }

    /**
     * @test
     */
    public function itFallsBackForGet()
    {
        $id = 'testID';
        $invoice = m::mock(InvoiceInterface::class);

        $this->invoiceRepository->shouldReceive('get')
            ->with($id)
            ->once()
            ->andReturn($invoice);

        $this->assertEquals($this->repository->get($id), $invoice);
    }

    /**
     * @test
     */
    public function itFallsBackForGetAll()
    {
        $invoices = [
            m::mock(InvoiceInterface::class),
            m::mock(InvoiceInterface::class),
            m::mock(InvoiceInterface::class),
        ];

        $this->invoiceRepository->shouldReceive('getAll')
            ->withNoArgs()
            ->once()
            ->andReturn($invoices);

        $this->assertEquals($this->repository->getAll(), $invoices);
    }

    /**
     * @test
     */
    public function itFallsBackForGetAllByCustomer()
    {
        $invoices = [
            m::mock(InvoiceInterface::class),
            m::mock(InvoiceInterface::class),
            m::mock(InvoiceInterface::class),
        ];
        $customer = m::mock(CustomerInterface::class);

        $this->invoiceRepository->shouldReceive('getAllByCustomer')
            ->with($customer)
            ->once()
            ->andReturn($invoices);

        $this->assertEquals($this->repository->getAllByCustomer($customer), $invoices);
    }
}
