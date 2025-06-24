<?php

namespace App\Models;

use App\Models\Scopes\YearScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $table = 'pagos';
    protected $primaryKey = 'mt';
    public $timestamps = false;
    protected $guarded = [];
    protected static function booted(): void
    {
        static::addGlobalScope(new YearScope);
    }
}
