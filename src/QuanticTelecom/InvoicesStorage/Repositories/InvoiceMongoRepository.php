<?php namespace QuanticTelecom\InvoicesStorage\Repositories;

use Carbon\Carbon;
use MongoCollection;
use MongoDate;
use MongoDB;
use MongoId;
use QuanticTelecom\Invoices\AbstractInvoice;
use QuanticTelecom\InvoicesStorage\Factories\CustomerFactoryInterface;
use QuanticTelecom\InvoicesStorage\Factories\GroupOfItemsFactoryInterface;
use QuanticTelecom\InvoicesStorage\Factories\InvoiceFactoryInterface;
use QuanticTelecom\InvoicesStorage\Factories\ItemFactoryInterface;
use QuanticTelecom\InvoicesStorage\Factories\PaymentFactoryInterface;

class InvoiceMongoRepository implements InvoiceRepositoryInterface
{
    protected $collection = "invoices";

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
     * @param MongoDB $database
     * @param InvoiceFactoryInterface $invoiceFactory
     * @param CustomerFactoryInterface $customerFactory
     * @param PaymentFactoryInterface $paymentFactory
     * @param ItemFactoryInterface $itemFactory
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
     * @param $id
     * @return AbstractInvoice
     */
    public function get($id)
    {
        $data = $this->getCollection()->findOne([
            'id' =>$id
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
        $invoices = [];
        $cursor = $this->getCollection()->find();

        foreach ($cursor as $id => $data) {
            $data = $this->transformDates($data);
            $invoices[$id] = $this->invoiceFactory->build($data['type'], $data);
        }

        return $invoices;
    }

    /**
     * Save an invoice.
     *
     * @param AbstractInvoice $invoice
     * @return mixed
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
                'items' => $items
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
     * @return MongoCollection
     */
    protected function getCollection()
    {
        return $this->database->{$this->collection};
    }

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
