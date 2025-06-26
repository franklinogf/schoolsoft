<?php

namespace App\Models;

use App\Models\Scopes\YearScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class CafeteriaOrderHistory extends Model
{
    protected $table = 'cafeteria_orders';
    protected $guarded = ['id'];
    public const string CREATED_AT = 'creado_en';

    // public $timestamps = false;

    protected static function booted(): void
    {
        static::addGlobalScope(new YearScope);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'ss', 'ss');
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(CafeteriaOrder::class, 'id_compra', 'id');
    }

    public function items(): HasManyThrough
    {
        return $this->hasManyThrough(
            CafeteriaOrderItem::class,
            CafeteriaOrder::class,
            'id', // Foreign key on CafeteriaOrder table...
            'id_compra', // Foreign key on CafeteriaOrderDetail table...
            'id', // Local key on CafeteriaOrderHistory table...
            'id' // Local key on CafeteriaOrder table...
        );
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('despachado', false);
    }
}
