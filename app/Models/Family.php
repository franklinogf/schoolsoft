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
}
