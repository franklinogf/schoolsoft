<?php

namespace App\Models;

use App\Models\Scopes\YearScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CafeteriaOrder extends Model
{
    protected $table = "compra_cafeteria";
    public $timestamps = false;
    protected $guarded = [];

    protected static function booted(): void
    {
        static::addGlobalScope(new YearScope);
    }
    public function items(): HasMany
    {
        return $this->hasMany(CafeteriaOrderItem::class, 'id_compra');
    }

    public function buyer()
    {
        return $this->belongsTo(Student::class, 'ss', 'ss');
    }
}
