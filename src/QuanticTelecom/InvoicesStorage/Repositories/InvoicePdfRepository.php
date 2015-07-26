<?php namespace QuanticTelecom\InvoicesStorage\Repositories;

use Illuminate\Filesystem\Filesystem;
use QuanticTelecom\Invoices\AbstractInvoice;
use QuanticTelecom\Invoices\Contracts\CustomerInterface;
use QuanticTelecom\Invoices\Contracts\PdfGeneratorInterface;
use QuanticTelecom\InvoicesStorage\Contracts\InvoiceRepositoryInterface;
use QuanticTelecom\InvoicesStorage\Contracts\PdfStorageInterface;

class InvoicePdfRepository implements InvoiceRepositoryInterface
{
    /**
     * @var InvoiceRepositoryInterface
     */
    protected $invoiceRepository;

    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * @var PdfGeneratorInterface
     */
    protected $pdfGenerator;

    /**
     * @var PdfStorageInterface
     */
    protected $pdfStorage;

    /**
     * @param InvoiceRepositoryInterface $invoiceRepository
     * @param Filesystem $filesystem
     * @param PdfGeneratorInterface $pdfGenerator
     * @param PdfStorageInterface $pdfStorage
     */
    public function __construct(
        InvoiceRepositoryInterface $invoiceRepository,
        Filesystem $filesystem,
        PdfGeneratorInterface $pdfGenerator,
        PdfStorageInterface $pdfStorage
    ) {
        $this->invoiceRepository = $invoiceRepository;
        $this->filesystem = $filesystem;
        $this->pdfGenerator = $pdfGenerator;
        $this->pdfStorage = $pdfStorage;
    }

    /**
     * Fetch one invoice by his ID.
     *
     * @param $id
     *
     * @return AbstractInvoice
     */
    public function get($id)
    {
        return $this->invoiceRepository->get($id);
    }

    /**
     * Fetch all invoices.
     *
     * @return AbstractInvoice[]
     */
    public function getAll()
    {
        return $this->invoiceRepository->getAll();
    }

    /**
     * Save an invoice.
     *
     * @param AbstractInvoice $invoice
     */
    public function save(AbstractInvoice $invoice)
    {
        // Save the invoice in the database
        $this->invoiceRepository->save($invoice);

        // Generate and save the invoice as a PDF
        $pdf = $this->pdfGenerator->generate($invoice);
        $this->filesystem->put(
            $this->pdfStorage->getStoragePath($invoice),
            $pdf
        );
    }

    /**
     * Get all invoices for a Customer.
     *
     * @param CustomerInterface $customer
     *
     * @return AbstractInvoice[]
     */
    public function getAllByCustomer(CustomerInterface $customer)
    {
        return $this->invoiceRepository->getAllByCustomer($customer);
    }
}
