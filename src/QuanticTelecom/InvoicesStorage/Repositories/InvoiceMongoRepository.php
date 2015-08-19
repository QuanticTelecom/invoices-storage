<?php

namespace QuanticTelecom\InvoicesStorage\Repositories;

use Carbon\Carbon;
use MongoCollection;
use MongoCursor;
use MongoDate;
use MongoDB;
use QuanticTelecom\Invoices\AbstractInvoice;
use QuanticTelecom\Invoices\Contracts\CustomerInterface;
use QuanticTelecom\InvoicesStorage\Contracts\CustomerFactoryInterface;
use QuanticTelecom\InvoicesStorage\Contracts\GroupOfItemsFactoryInterface;
use QuanticTelecom\InvoicesStorage\Contracts\InvoiceFactoryInterface;
use QuanticTelecom\InvoicesStorage\Contracts\InvoiceRepositoryInterface;
use QuanticTelecom\InvoicesStorage\Contracts\ItemFactoryInterface;
use QuanticTelecom\InvoicesStorage\Contracts\LastInvoiceRepositoryInterface;
use QuanticTelecom\InvoicesStorage\Contracts\PaymentFactoryInterface;

/**
 * Class InvoiceMongoRepository.
 */
class InvoiceMongoRepository implements InvoiceRepositoryInterface, LastInvoiceRepositoryInterface
{
    /**
     * @var InvoiceFactoryInterface
     */
    private $invoiceFactory;

    /**
     * @var MongoCollection
     */
    private $collection;

    /**
     * @param MongoCollection              $collection
     * @param InvoiceFactoryInterface      $invoiceFactory
     */
    public function __construct(
        MongoCollection $collection,
        InvoiceFactoryInterface $invoiceFactory
    ) {
        $this->collection = $collection;
        $this->invoiceFactory = $invoiceFactory;
    }

    /**
     * Fetch one invoice by his ID.
     *
     * @param string $id
     *
     * @return AbstractInvoice
     */
    public function get($id)
    {
        $data = $this->collection->findOne([
            'id' => $id,
        ]);
        $data = $this->transformDates($data);

        return $this->invoiceFactory->build($data['type'], $data);
    }

    /**
     * Fetch all invoices.
     *
     * @return AbstractInvoice[]
     */
    public function getAll()
    {
        $cursor = $this->collection->find();

        return $this->cursorToInvoiceArray($cursor);
    }

    /**
     * Save an invoice.
     *
     * @param AbstractInvoice $invoice
     */
    public function save(AbstractInvoice $invoice)
    {
        $document = $this->invoiceFactory->toArray($invoice);

        $this->collection->insert($document);
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
        $cursor = $this->collection->find([
            'customer.id' => $customer->getCustomerId(),
        ]);

        return $this->cursorToInvoiceArray($cursor);
    }

    /**
     * Get the last invoice saved for the month provided by the Carbon date.
     *
     * @param Carbon $date
     *
     * @return AbstractInvoice|null the last invoice for the provided date or null if there is no invoice
     */
    public function getLastInvoiceForMonth(Carbon $date)
    {
        $invoiceData = $this->collection->aggregate([
            '$project' => [
                'year' => ['$year' => '$createdAt'],
                'month' => ['$month' => '$createdAt'],
                'type' => 1,
                'id' => 1,
                'customer' => 1,
                'createdAt' => 1,
                'dueDate' => 1,
                'includingTaxTotalPrice' => 1,
                'excludingTaxTotalPrice' => 1,
                'vatAmount' => 1,
            ],
        ], [
            '$match' => [
                'year' => $date->year,
                'month' => $date->month,
            ],
        ], [
            '$sort' => ['createdAt' => -1],
        ], [
            '$limit' => 1,
        ]);

        if (count($invoiceData['result']) != 0) {
            $invoiceData = $this->transformDates($invoiceData['result'][0]);

            return $this->invoiceFactory->build($invoiceData['type'], $invoiceData);
        } else {
            return null;
        }
    }

    /**
     * Transform a MongoCursor into an array of AbstractInvoice.
     *
     * @param MongoCursor $cursor
     *
     * @return AbstractInvoice[]
     */
    protected function cursorToInvoiceArray(MongoCursor $cursor)
    {
        $invoices = [];

        foreach ($cursor as $id => $data) {
            $data = $this->transformDates($data);
            $invoices[$id] = $this->invoiceFactory->build($data['type'], $data);
        }

        return $invoices;
    }

    /**
     * Transform all dates in the $data array into Carbon instance.
     *
     * @param array $data
     *
     * @return array
     */
    protected function transformDates($data)
    {
        foreach ($data as $key => $value) {
            if ($value instanceof MongoDate) {
                $data[$key] = Carbon::createFromTimeStamp($value->sec);
            }
            if (is_array($value)) {
                $data[$key] = $this->transformDates($value);
            }
        }

        return $data;
    }
}
