<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
