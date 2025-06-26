<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CafeteriaOrderItem extends Model
{
    protected $table = "compra_cafeteria_detalle";
    public $timestamps = false;
    protected $guarded = [];

    public function order(): BelongsTo
    {
        return $this->belongsTo(StoreOrder::class, "id_compra");
    }
}
