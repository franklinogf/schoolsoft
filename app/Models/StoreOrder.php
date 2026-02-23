<?php

namespace App\Models;

use App\Enums\OrderPaymentTypeEnum;
use App\Models\Scopes\YearScope;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Collection;

/**
 * @property int $id
 * @property string $accountID
 * @property string $trxID
 * @property string $customerName
 * @property string $customerEmail
 * @property string $refNumber
 * @property CarbonInterface $date
 * @property float $subtotal
 * @property float $ivu
 * @property float $total
 * @property string $deliveryTo
 * @property int $shopping
 * @property string $year
 * @property int $paid
 * @property OrderPaymentTypeEnum $payment_type
 * @property Collection<int, StoreOrderItem> $items
 * @property Student|null $receiver
 */
class StoreOrder extends Model
{
    protected $table = "compras";
    public $timestamps = false;
    protected $guarded = [];

    protected static function booted(): void
    {
        static::addGlobalScope(new YearScope);
    }

    public function items(): HasMany
    {
        return $this->hasMany(StoreOrderItem::class, 'id_compra');
    }

    public function receiver(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'deliveryTo', 'ss')->withoutGlobalScopes();
    }

    public function casts()
    {
        return [
            'date' => 'datetime',
            'payment_type' => OrderPaymentTypeEnum::class,
        ];
    }
}
