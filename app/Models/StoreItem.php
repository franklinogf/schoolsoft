<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $store_id
 * @property string $name
 * @property float $price
 * @property string $options
 * @property int $buy_multiple
 * @property string $picture_url
 * @property string $created_at
 * @property string $updated_at
 * @property Store|null $store
 */
class StoreItem extends Model
{
    public $timestamps = false;
    protected $guarded = [];


    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }
}
