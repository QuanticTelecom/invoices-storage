<?php namespace QuanticTelecom\InvoicesStorage\Tests;

use Carbon\Carbon;
use PHPUnit_Framework_TestCase;
use Mockery as m;
use QuanticTelecom\Invoices\Contracts\CustomerInterface;
use QuanticTelecom\Invoices\Contracts\GroupOfItemsInterface;
use QuanticTelecom\Invoices\Contracts\InvoiceInterface;
use QuanticTelecom\Invoices\Contracts\ItemInterface;
use QuanticTelecom\Invoices\Contracts\PaymentInterface;
use QuanticTelecom\Invoices\Tests\Helpers\InvoiceStubFactoryTrait;
use QuanticTelecom\InvoicesStorage\Factories\CustomerFactoryInterface;
use QuanticTelecom\InvoicesStorage\Factories\GroupOfItemsFactoryInterface;
use QuanticTelecom\InvoicesStorage\Factories\InvoiceFactory;
use QuanticTelecom\InvoicesStorage\Factories\ItemFactoryInterface;
use QuanticTelecom\InvoicesStorage\Factories\PaymentFactoryInterface;

abstract class InvoiceStorageTest extends PHPUnit_Framework_TestCase
{
    use InvoiceStubFactoryTrait;

    /**
     * @var CustomerInterface
     */
    protected $customer;

    /**
     * @var CustomerFactoryInterface
     */
    protected $customerFactory;

    /**
     * @var PaymentInterface
     */
    protected $payment;

    /**
     * @var PaymentFactoryInterface
     */
    protected $paymentFactory;

    /**
     * @var ItemInterface
     */
    protected $item;

    /**
     * @var ItemFactoryInterface
     */
    protected $itemFactory;

    /**
     * @var GroupOfItemsInterface
     */
    protected $groupOfItems;

    /**
     * @var GroupOfItemsFactoryInterface
     */
    protected $groupOfItemsFactory;

    /**
     * @var InvoiceFactory
     */
    protected $invoiceFactory;

    public function setUp()
    {
        $this->customer = m::mock(CustomerInterface::class);
        $this->customerFactory = m::mock(CustomerFactoryInterface::class);
        $this->customerFactory->shouldReceive('build')->andReturn($this->customer);

        $this->payment = m::mock(PaymentInterface::class);
        $this->paymentFactory = m::mock(PaymentFactoryInterface::class);
        $this->paymentFactory->shouldReceive('build')->andReturn($this->payment);

        $this->item = m::mock(ItemInterface::class);
        $this->itemFactory = m::mock(ItemFactoryInterface::class);
        $this->itemFactory->shouldReceive('build')->andReturn($this->item);

        $this->groupOfItems = m::mock(GroupOfItemsInterface::class);
        $this->groupOfItemsFactory = m::mock(GroupOfItemsFactoryInterface::class);
        $this->groupOfItemsFactory->shouldReceive('build')->andReturn($this->groupOfItems);

        $this->invoiceFactory = new InvoiceFactory(
            $this->customerFactory,
            $this->paymentFactory,
            $this->itemFactory,
            $this->groupOfItemsFactory
        );
    }

    public function tearDown()
    {
        m::close();
    }

    protected function assertAllDataAreSet(InvoiceInterface $invoice, $data)
    {
        $this->assertEquals($invoice->getId(), $data['id']);
        $this->assertEquals($invoice->getCustomer(), $this->customer);

        $this->assertInstanceOf(Carbon::class, $invoice->getCreatedAt());
        $this->assertEquals($invoice->getCreatedAt(), $data['createdAt']);

        $this->assertInstanceOf(Carbon::class, $invoice->getDueDate());
        $this->assertEquals($invoice->getDueDate(), $data['dueDate']);
    }

    protected function assertPaymentIsSet(InvoiceInterface $invoice)
    {
        $this->assertTrue($invoice->isPaid());
        $this->assertInstanceOf(PaymentInterface::class, $invoice->getPayment());
    }

    /**
     * @param InvoiceInterface $invoice
     * @param array $itemsData
     */
    protected function assertItemsAreSet(InvoiceInterface $invoice, $itemsData)
    {
        $this->assertCount(count($itemsData), $invoice->getItems());

        foreach ($invoice->getItems() as $item) {
            $this->assertInstanceOf(ItemInterface::class, $item);
        }
    }

    /**
     * @param InvoiceInterface $invoice
     * @param array $groupsData
     */
    protected function assertGroupsAreSet(InvoiceInterface $invoice, $groupsData)
    {
        $this->assertCount(count($groupsData), $invoice->getGroups());

        foreach ($invoice->getGroups() as $group) {
            $this->assertInstanceOf(GroupOfItemsInterface::class, $group);
        }
    }

    protected function getFactoryData($options = [])
    {
        $data = [
            'id' => $this->invoiceData['id'],
            'customer' => [
                'type' => 'customer'
            ],
            'createdAt' => Carbon::createFromFormat('Y-m-j', $this->invoiceData['createdAt']),
            'dueDate' => Carbon::createFromFormat('Y-m-j', $this->invoiceData['dueDate']),
        ];

        if (isset($options['payment']) and $options['payment'] === true) {
            $data['payment'] = $this->paymentData;
            $data['payment']['type'] = 'paymentType';
        }

        if (isset($options['items']) and $options['items'] === true) {
            $data['items'] = $this->itemsData;

            foreach ($data['items'] as $key => $item) {
                $data['items'][$key]['type'] = 'itemType';
            }
        }

        if (isset($options['groups']) and $options['groups'] === true) {
            $data['groups'] = $this->groupsData;

            foreach ($data['groups'] as $key => $group) {
                $data['groups'][$key]['type'] = 'groupType';
            }
        }

        return $data;
    }
}
