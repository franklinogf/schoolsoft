<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StoreItem extends Model
{
    public $timestamps = false;
    protected $guarded = [];


    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }
}
