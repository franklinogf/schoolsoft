<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Builder;
use App\Models\Traits\AlphaAndNumber;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;


class Family extends Model
{
    use AlphaAndNumber;
    protected $table = 'madre';
    protected $primaryKey = 'id';
    public $timestamps = false;
    protected $guarded = [];

    public function kids(): HasMany
    {
        return $this->hasMany(Student::class, 'id', 'id');
    }

    public function charges(): HasMany
    {
        return $this->hasMany(Payment::class, 'id', 'id');
    }

    public function payments(): HasMany
    {
        return $this->charges()->whereDate('fecha_d', '!=', '0000-00-00');
    }

    public function debts(): HasMany
    {
        return $this->charges()->whereDate('fecha_d',  '0000-00-00');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->whereHas('kids');
    }
}
