<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $table = 'year';
    protected $primaryKey = 'mt';
    public $timestamps = false;
    protected $guarded = [];

    public function scopeUnerolled(Builder $query)
    {
        $query->where('codigobaja', '0');
    }
}
