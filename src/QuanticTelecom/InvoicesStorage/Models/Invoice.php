<?php namespace QuanticTelecom\InvoicesStorage\Models;

use Jenssegers\Mongodb\Relations\EmbedsMany;
use QuanticTelecom\Storage\Model;

class Invoice extends Model
{
    /**
     * The collection associated with the model.
     *
     * @var string
     */
    protected $collection = "invoices";

    /**
     * The Customer associated with the invoice.
     *
     * @return EmbedsMany
     */
    public function customer()
    {
        return $this->embedsOne(Customer::class);
    }

    /**
     * The Payment associated with the invoice.
     *
     * @return EmbedsMany
     */
    public function payment()
    {
        return $this->embedsOne(Payment::class);
    }

    /**
     * The Item models associated with the invoice.
     *
     * @return EmbedsMany
     */
    public function items()
    {
        return $this->embedsMany(Item::class);
    }
}
