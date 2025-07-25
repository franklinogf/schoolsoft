<?php

namespace App\Models;

use App\Models\Scopes\YearScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;


class Family extends Model
{
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
}
