<?php

namespace App\Models;

use App\Models\Scopes\YearScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Classes extends Model
{
    protected $table = 'padres';
    protected $primaryKey = 'aa';
    public $timestamps = false;
    protected $guarded = [];

    protected static function booted(): void
    {
        static::addGlobalScope(new YearScope);
    }

    public function scopeTable(Builder $query, string $table): Builder
    {
        return $query->from($table);
    }
}
