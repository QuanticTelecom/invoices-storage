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
     * Collection name of the invoices.
     *
     * @var string
     */
    protected $collection = 'invoices';

    /**
     * @var InvoiceFactoryInterface
     */
    private $invoiceFactory;

    /**
     * @var MongoDB
     */
    private $database;

    /**
     * @var CustomerFactoryInterface
     */
    private $customerFactory;

    /**
     * @var PaymentFactoryInterface
     */
    private $paymentFactory;

    /**
     * @var ItemFactoryInterface
     */
    private $itemFactory;

    /**
     * @var GroupOfItemsFactoryInterface
     */
    private $groupOfItemsFactory;

    /**
     * @param MongoDB                      $database
     * @param InvoiceFactoryInterface      $invoiceFactory
     * @param CustomerFactoryInterface     $customerFactory
     * @param PaymentFactoryInterface      $paymentFactory
     * @param ItemFactoryInterface         $itemFactory
     * @param GroupOfItemsFactoryInterface $groupOfItemsFactory
     */
    public function __construct(
        MongoDB $database,
        InvoiceFactoryInterface $invoiceFactory,
        CustomerFactoryInterface $customerFactory,
        PaymentFactoryInterface $paymentFactory,
        ItemFactoryInterface $itemFactory,
        GroupOfItemsFactoryInterface $groupOfItemsFactory
    ) {
        $this->database = $database;
        $this->invoiceFactory = $invoiceFactory;
        $this->customerFactory = $customerFactory;
        $this->paymentFactory = $paymentFactory;
        $this->itemFactory = $itemFactory;
        $this->groupOfItemsFactory = $groupOfItemsFactory;
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
        $data = $this->getCollection()->findOne([
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
        $cursor = $this->getCollection()->find();

        return $this->cursorToInvoiceArray($cursor);
    }

    /**
     * Save an invoice.
     *
     * @param AbstractInvoice $invoice
     */
    public function save(AbstractInvoice $invoice)
    {
        $document = [
            'type' => $this->invoiceFactory->inverseResolution($invoice),
            'id' => $invoice->getId(),
            'customer' => [
                'type' => $this->customerFactory->inverseResolution($invoice->getCustomer()),
                'id' => $invoice->getCustomer()->getCustomerId(),
                'name' => $invoice->getCustomer()->getCustomerName(),
                'address' => $invoice->getCustomer()->getCustomerAddress(),
            ],
            'createdAt' => new MongoDate($invoice->getCreatedAt()->timestamp),
            'dueDate' => new MongoDate($invoice->getDueDate()->timestamp),
            'includingTaxTotalPrice' => $invoice->getIncludingTaxTotalPrice(),
            'excludingTaxTotalPrice' => $invoice->getExcludingTaxTotalPrice(),
            'vatAmount' => $invoice->getVatAmount(),
        ];

        $items = [];
        foreach ($invoice->getItems() as $item) {
            $items[] = [
                'type' => $this->itemFactory->inverseResolution($item),
                'name' => $item->getItemName(),
                'quantity' => $item->getItemQuantity(),
                'includingTaxUnitPrice' => $item->getItemIncludingTaxUnitPrice(),
                'includingTaxTotalPrice' => $item->getItemIncludingTaxTotalPrice(),
                'excludingTaxUnitPrice' => $item->getItemExcludingTaxUnitPrice(),
                'excludingTaxTotalPrice' => $item->getItemExcludingTaxTotalPrice(),
            ];
        }
        $document['items'] = $items;

        $groups = [];
        foreach ($invoice->getGroups() as $group) {
            $items = [];
            foreach ($group->getItems() as $item) {
                $items[] = [
                    'type' => $this->itemFactory->inverseResolution($item),
                    'name' => $item->getItemName(),
                    'quantity' => $item->getItemQuantity(),
                    'includingTaxUnitPrice' => $item->getItemIncludingTaxUnitPrice(),
                    'includingTaxTotalPrice' => $item->getItemIncludingTaxTotalPrice(),
                    'excludingTaxUnitPrice' => $item->getItemExcludingTaxUnitPrice(),
                    'excludingTaxTotalPrice' => $item->getItemExcludingTaxTotalPrice(),
                ];
            }

            $groups[] = [
                'type' => $this->groupOfItemsFactory->inverseResolution($group),
                'name' => $group->getName(),
                'items' => $items,
            ];
        }
        $document['groups'] = $groups;

        if ($invoice->isPaid()) {
            $document['payment'] = [
                'type' => $this->paymentFactory->inverseResolution($invoice->getPayment()),
                'name' => $invoice->getPayment()->getPaymentName(),
                'date' => new MongoDate($invoice->getPayment()->getPaymentDate()->timestamp),
            ];
        }

        $this->getCollection()->insert($document);
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
        $cursor = $this->getCollection()->find([
            'customer.id' => $customer->getCustomerId(),
        ]);

        return $this->cursorToInvoiceArray($cursor);
    }

    /**
     * Get the last invoice saved for the month provided by the Carbon date.
     *
     * @param Carbon $date
     *
     * @return AbstractInvoice
     */
    public function getLastInvoiceForMonth(Carbon $date)
    {
        $invoiceData = $this->getCollection()->aggregate([
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
     * @return MongoCollection
     */
    protected function getCollection()
    {
        return $this->database->{$this->collection};
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
