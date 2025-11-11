<?php

namespace App\Models;

use App\Models\Scopes\YearScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkPlan4 extends Model
{
    protected $table = 'plan_trabajo4';
    protected $primaryKey = 'id';
    public $timestamps = false;
    protected $guarded = [];

    protected static function booted(): void
    {
        static::addGlobalScope(new YearScope);
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class, 'id_profesor', 'id');
    }

    public function scopeByTeacher(Builder $query, int $teacherId): Builder
    {
        return $query->where('id_profesor', $teacherId);
    }
}
