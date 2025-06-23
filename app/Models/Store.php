<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
