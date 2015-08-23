<?php

namespace QuanticTelecom\InvoicesStorage\Factories;

use MongoDate;
use QuanticTelecom\Invoices\AbstractInvoice;
use QuanticTelecom\Invoices\ExcludingTaxInvoice;
use QuanticTelecom\Invoices\IncludingTaxInvoice;
use QuanticTelecom\InvoicesStorage\Contracts\CustomerFactoryInterface;
use QuanticTelecom\InvoicesStorage\Contracts\GroupOfItemsFactoryInterface;
use QuanticTelecom\InvoicesStorage\Contracts\InvoiceArrayValidatorInterface;
use QuanticTelecom\InvoicesStorage\Contracts\InvoiceFactoryInterface;
use QuanticTelecom\Invoices\Contracts\InvoiceInterface;
use QuanticTelecom\InvoicesStorage\Contracts\ItemFactoryInterface;
use QuanticTelecom\InvoicesStorage\Contracts\PaymentFactoryInterface;
use QuanticTelecom\InvoicesStorage\Exceptions\InvoiceFactory\InvalidDataForInvoiceFactoryException;
use QuanticTelecom\InvoicesStorage\Exceptions\InvoiceFactory\InvoiceTypeNotFoundException;
use QuanticTelecom\InvoicesStorage\Exceptions\InvoiceFactory\UnknownInvoiceClassException;
use QuanticTelecom\InvoicesStorage\IdGenerator;

/**
 * Class InvoiceFactory.
 */
class InvoiceFactory implements InvoiceFactoryInterface
{
    use ItemsContainerFactoryTrait;
    use GroupsContainerFactoryTrait;

    /**
     * @var CustomerFactoryInterface
     */
    protected $customerFactory;

    /**
     * @var PaymentFactoryInterface
     */
    protected $paymentFactory;

    /**
     * @var ItemFactoryInterface
     */
    protected $itemFactory;

    /**
     * @var GroupOfItemsFactoryInterface
     */
    protected $groupOfItemsFactory;

    /**
     * @var InvoiceArrayValidatorInterface
     */
    protected $invoiceArrayValidator;

    /**
     * @param CustomerFactoryInterface       $customerFactory
     * @param PaymentFactoryInterface        $paymentFactory
     * @param ItemFactoryInterface           $itemFactory
     * @param GroupOfItemsFactoryInterface   $groupOfItemsFactory
     * @param InvoiceArrayValidatorInterface $invoiceArrayValidator
     */
    public function __construct(
        CustomerFactoryInterface $customerFactory,
        PaymentFactoryInterface $paymentFactory,
        ItemFactoryInterface $itemFactory,
        GroupOfItemsFactoryInterface $groupOfItemsFactory,
        InvoiceArrayValidatorInterface $invoiceArrayValidator
    ) {
        $this->customerFactory = $customerFactory;
        $this->paymentFactory = $paymentFactory;
        $this->itemFactory = $itemFactory;
        $this->groupOfItemsFactory = $groupOfItemsFactory;
        $this->invoiceArrayValidator = $invoiceArrayValidator;
    }

    /**
     * Build a new InvoiceInterface instance.
     *
     * @param string $type type of invoice
     * @param array  $data all data to create an invoice
     *
     * @return InvoiceInterface
     *
     * @throws InvalidDataForInvoiceFactoryException|InvoiceTypeNotFoundException
     */
    public function build($type, $data = [])
    {
        switch ($type) {
            case 'excludingTaxInvoice':
                return $this->buildExcludingTaxInvoice($data);
                break;
            case 'includingTaxInvoice':
                return $this->buildIncludingTaxInvoice($data);
                break;
            default:
                throw new InvoiceTypeNotFoundException();
        }
    }

    /**
     * Get the type of invoice.
     *
     * @param InvoiceInterface $class
     *
     * @return string type of invoice
     *
     * @throws UnknownInvoiceClassException
     */
    public function inverseResolution(InvoiceInterface $class)
    {
        if ($class instanceof ExcludingTaxInvoice) {
            return 'excludingTaxInvoice';
        } elseif ($class instanceof IncludingTaxInvoice) {
            return 'includingTaxInvoice';
        } else {
            throw new UnknownInvoiceClassException();
        }
    }

    /**
     * Build an AbstractInvoice.
     *
     * @param $class AbstractInvoice
     * @param array $data
     *
     * @return AbstractInvoice
     *
     * @throws InvalidDataForInvoiceFactoryException
     */
    protected function buildAbstractInvoice($class, $data = [])
    {
        if (!$this->invoiceArrayValidator->validate($data)) {
            throw new InvalidDataForInvoiceFactoryException();
        }

        $idGenerator = new IdGenerator($data['id']);
        $customer = $this->customerFactory->build($data['customer']['type'], $data['customer']);

        $invoice = new $class($idGenerator, $customer, $data['dueDate'], $data['createdAt']);
        $this->fillInvoice($invoice, $data);

        return $invoice;
    }

    /**
     * Build an ExcludingTaxInvoice implementation.
     *
     * @param array $data
     *
     * @return ExcludingTaxInvoice
     *
     * @throws InvalidDataForInvoiceFactoryException
     */
    protected function buildExcludingTaxInvoice($data = [])
    {
        return $this->buildAbstractInvoice(ExcludingTaxInvoice::class, $data);
    }

    /**
     * Build an IncludingTaxInvoice implementation.
     *
     * @param array $data
     *
     * @return IncludingTaxInvoice
     *
     * @throws InvalidDataForInvoiceFactoryException
     */
    protected function buildIncludingTaxInvoice($data = [])
    {
        return $this->buildAbstractInvoice(IncludingTaxInvoice::class, $data);
    }

    /**
     * Fill the invoice with all required data.
     *
     * @param InvoiceInterface $invoice
     * @param array            $data
     */
    protected function fillInvoice(InvoiceInterface $invoice, $data = [])
    {
        if ($this->checkPayment($data)) {
            $payment = $this->paymentFactory->build($data['payment']['type'], $data['payment']);
            $invoice->setPayment($payment);
        }

        if (isset($data['items']) and is_array($data['items'])) {
            $this->fillItems($invoice, $this->itemFactory, $data['items']);
        }

        if (isset($data['groups']) and is_array($data['groups'])) {
            $this->fillGroups($invoice, $this->groupOfItemsFactory, $data['groups']);
        }
    }

    /**
     * Check if there is a payment in the data array.
     *
     * @param array $data
     *
     * @return bool
     *
     * @throws InvalidDataForInvoiceFactoryException
     */
    protected function checkPayment($data = [])
    {
        if (isset($data['payment'])) {
            if (is_array($data['payment']) and array_key_exists('type', $data['payment'])) {
                return true;
            } else {
                throw new InvalidDataForInvoiceFactoryException();
            }
        } else {
            return false;
        }
    }

    /**
     * Transform an invoice into an array of data.
     *
     * @param InvoiceInterface $invoice
     *
     * @return array
     */
    public function toArray(InvoiceInterface $invoice)
    {
        $data = [
            'type' => $this->inverseResolution($invoice),
            'id' => $invoice->getId(),
            'customer' => $this->customerFactory->toArray($invoice->getCustomer()),
            'createdAt' => new MongoDate($invoice->getCreatedAt()->timestamp),
            'dueDate' => new MongoDate($invoice->getDueDate()->timestamp),
            'includingTaxTotalPrice' => $invoice->getIncludingTaxTotalPrice(),
            'excludingTaxTotalPrice' => $invoice->getExcludingTaxTotalPrice(),
            'vatAmount' => $invoice->getVatAmount(),
            'items' => [],
            'groups' => [],
        ];

        foreach ($invoice->getItems() as $item) {
            $data['items'][] = $this->itemFactory->toArray($item);
        }

        foreach ($invoice->getGroups() as $group) {
            $data['groups'][] = $this->groupOfItemsFactory->toArray($group);
        }

        if ($invoice->isPaid()) {
            $data['payment'] = $this->paymentFactory->toArray($invoice->getPayment());
        }

        return $data;
    }
}
