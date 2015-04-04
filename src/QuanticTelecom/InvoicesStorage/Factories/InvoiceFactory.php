<?php namespace QuanticTelecom\InvoicesStorage\Factories;

use QuanticTelecom\Invoices\AbstractInvoice;
use QuanticTelecom\Invoices\Contracts\InvoiceInterface;
use QuanticTelecom\Invoices\ExcludingTaxInvoice;
use QuanticTelecom\Invoices\IncludingTaxInvoice;
use QuanticTelecom\InvoicesStorage\Exceptions\InvoiceFactory\InvalidDataForInvoiceFactoryException;
use QuanticTelecom\InvoicesStorage\Exceptions\InvoiceFactory\InvoiceTypeNotFoundException;
use QuanticTelecom\InvoicesStorage\Exceptions\InvoiceFactory\UnknownInvoiceClassException;
use QuanticTelecom\InvoicesStorage\IdGenerator;

/**
 * Class InvoiceFactory
 * @package QuanticTelecom\InvoicesStorage\Factories
 */
class InvoiceFactory implements InvoiceFactoryInterface
{
    use ItemsContainerFactoryTrait;
    use GroupsContainerFactoryTrait;

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
     * @param CustomerFactoryInterface $customerFactory
     * @param PaymentFactoryInterface $paymentFactory
     * @param ItemFactoryInterface $itemFactory
     * @param GroupOfItemsFactoryInterface $groupOfItemsFactory
     */
    public function __construct(
        CustomerFactoryInterface $customerFactory,
        PaymentFactoryInterface $paymentFactory,
        ItemFactoryInterface $itemFactory,
        GroupOfItemsFactoryInterface $groupOfItemsFactory
    ) {
        $this->customerFactory = $customerFactory;
        $this->paymentFactory = $paymentFactory;
        $this->itemFactory = $itemFactory;
        $this->groupOfItemsFactory = $groupOfItemsFactory;
    }

    /**
     * Build a new InvoiceInterface instance.
     *
     * @param string $type type of invoice
     * @param array $data all data to create an invoice
     * @return InvoiceInterface
     * @throws InvalidDataForInvoiceFactoryException|InvoiceTypeNotFoundException
     */
    public function build($type, $data = [])
    {
        switch ($type) {
            case "excludingTaxInvoice":
                return $this->buildExcludingTaxInvoice($data);
                break;
            case "includingTaxInvoice":
                return $this->buildIncludingTaxInvoice($data);
                break;
            default:
                throw new InvoiceTypeNotFoundException;
        }
    }

    /**
     * Get the type of invoice.
     *
     * @param InvoiceInterface $class
     * @return string type of invoice
     * @throws UnknownInvoiceClassException
     */
    public function inverseResolution(InvoiceInterface $class)
    {
        if ($class instanceof ExcludingTaxInvoice) {
            return "excludingTaxInvoice";
        } elseif ($class instanceof IncludingTaxInvoice) {
            return "includingTaxInvoice";
        } else {
            throw new UnknownInvoiceClassException;
        }
    }

    /**
     * Build an AbstractInvoice.
     *
     * @param $class AbstractInvoice
     * @param array $data
     * @return AbstractInvoice
     * @throws InvalidDataForInvoiceFactoryException
     */
    private function buildAbstractInvoice($class, $data = [])
    {
        if (!$this->checkData($data)) {
            throw new InvalidDataForInvoiceFactoryException;
        }

        $idGenerator = new IdGenerator($data['id']);
        $customer = $this->customerFactory->build($data['customer']['type'], $data['customer']);

        $invoice = new $class($idGenerator, $customer);
        $this->fillInvoice($invoice, $data);

        return $invoice;
    }

    /**
     * Build an ExcludingTaxInvoice implementation.
     *
     * @param array $data
     * @return ExcludingTaxInvoice
     * @throws InvalidDataForInvoiceFactoryException
     */
    private function buildExcludingTaxInvoice($data = [])
    {
        return $this->buildAbstractInvoice(ExcludingTaxInvoice::class, $data);
    }

    /**
     * Build an IncludingTaxInvoice implementation.
     *
     * @param array $data
     * @return IncludingTaxInvoice
     * @throws InvalidDataForInvoiceFactoryException
     */
    private function buildIncludingTaxInvoice($data = [])
    {
        return $this->buildAbstractInvoice(IncludingTaxInvoice::class, $data);
    }

    /**
     * Check if all necessary data is present
     *
     * @param array $data
     * @return bool
     */
    private function checkData($data = [])
    {
        return  array_key_exists('id', $data)
            and array_key_exists('customer', $data)
            and is_array($data['customer'])
            and array_key_exists('type', $data['customer']);
    }

    /**
     * Fill the invoice with all required data.
     *
     * @param InvoiceInterface $invoice
     * @param array $data
     */
    private function fillInvoice(InvoiceInterface $invoice, $data = [])
    {
        if (isset($data['createdAt'])) {
            $invoice->setCreatedAt($data['createdAt']);
        }

        if (isset($data['dueDate'])) {
            $invoice->setDueDate($data['dueDate']);
        }

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
     * @return bool
     * @throws InvalidDataForInvoiceFactoryException
     */
    private function checkPayment($data = [])
    {
        if (isset($data['payment'])) {
            if (is_array($data['payment']) and array_key_exists('type', $data['payment'])) {
                return true;
            } else {
                throw new InvalidDataForInvoiceFactoryException;
            }
        } else {
            return false;
        }
    }
}
