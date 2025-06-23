<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
