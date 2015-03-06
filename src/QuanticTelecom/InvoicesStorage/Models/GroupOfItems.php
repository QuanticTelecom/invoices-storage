<?php namespace QuanticTelecom\InvoicesStorage\Models;

use Jenssegers\Mongodb\Relations\EmbedsMany;
use QuanticTelecom\Storage\Model;

class GroupOfItems extends Model
{
    /**
     * The Item models associated with the group of items.
     *
     * @return EmbedsMany
     */
    public function items()
    {
        return $this->embedsMany(Item::class);
    }

    /**
     * The GroupOfItems models associated with the group of items.
     *
     * @return EmbedsMany
     */
    public function groups()
    {
        return $this->embedsMany(GroupOfItems::class);
    }
}
