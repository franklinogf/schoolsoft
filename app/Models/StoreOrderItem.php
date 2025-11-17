<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $id_compra
 * @property string $item_name
 * @property int $amount
 * @property string $size
 * @property float $price
 * @property int $orden
 * @property string $year
 * @property StoreOrder|null $order
 */
class StoreOrderItem extends Model
{
    protected $table = "compras_detalle";
    public $timestamps = false;
    protected $guarded = [];

    public function order(): BelongsTo
    {
        return $this->belongsTo(StoreOrder::class, "id_compra");
    }
}
