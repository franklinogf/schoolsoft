<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class CafeteriaButton extends Model
{
    protected $table = 'T_cafeteria';
    protected $primaryKey = 'id';
    protected $guarded = [];

    public $timestamps = false;
    protected static function booted(): void
    {

        static::addGlobalScope('order', function (Builder $builder) {
            $builder->orderBy('orden');
        });
    }
}
