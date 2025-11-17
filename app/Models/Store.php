<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

/**
 * @property int $id
 * @property string $name
 * @property string $description
 * @property int $active
 * @property string $prefix_code
 * @property string $created_at
 * @property string $updated_at
 * @property Collection<int, StoreItem> $items
 */
class Store extends Model
{
    public $timestamps = false;
    protected $guarded = [];


    public function scopeActive(Builder $query): Builder
    {
        return $query->where('active', true);
    }

    public function items(): HasMany
    {
        return $this->hasMany(StoreItem::class);
    }
}
