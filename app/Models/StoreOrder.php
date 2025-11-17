<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

/**
 * @property int $id
 * @property string $accountID
 * @property string $trxID
 * @property string $customerName
 * @property string $customerEmail
 * @property string $refNumber
 * @property string $date
 * @property float $subtotal
 * @property float $ivu
 * @property float $total
 * @property string $deliveryTo
 * @property int $shopping
 * @property string $year
 * @property int $paid
 * @property string $payment_type
 * @property Collection<int, StoreOrderItem> $items
 */
class StoreOrder extends Model
{
    protected $table = "compras";
    public $timestamps = false;
    protected $guarded = [];

    public function items(): HasMany
    {
        return $this->hasMany(StoreOrderItem::class, 'id_compra');
    }
}
