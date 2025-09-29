<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Builder;
use App\Models\Traits\AlphaAndNumber;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;


/**
 * @property int $id
 * @property string $email_p
 * @property string $padre
 * @property string $email_m
 * @property string $madre
 * @property Collection<int, Student> $kids
 * @property Collection<int, Payment> $charges
 * @property Collection<int, Payment> $payments
 * @property Collection<int, Payment> $debts
 * 
 */
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
