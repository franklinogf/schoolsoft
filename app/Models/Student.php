<?php

namespace App\Models;

use App\Enums\Gender;
use App\Models\Scopes\YearScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Student extends Model
{
    protected $table = 'year';
    protected $primaryKey = 'mt';
    public $timestamps = false;
    protected $guarded = [];

    protected static function booted(): void
    {
        static::addGlobalScope(new YearScope);
    }

    public function scopeUnerolled(Builder $query)
    {
        $query->where('codigobaja', '0');
    }

    public function family(): BelongsTo
    {
        return $this->belongsTo(Family::class, 'id', 'id');
    }

    public function casts()
    {
        return [
            'fecha' => 'date:Y-m-d',
            'fecha_matri' => 'date:Y-m-d',
            // 'genero' => Gender::class,
        ];
    }


    protected function profilePicture(): Attribute
    {
        return Attribute::make(
            get: fn($value, array $attributes) =>
            $attributes['imagen'] !== '' ? school_asset(__STUDENT_PROFILE_PICTURE_URL . $attributes['imagen'])
                : ($attributes['genero'] === Gender::FEMALE->value
                    ? __NO_PROFILE_PICTURE_STUDENT_FEMALE
                    : __NO_PROFILE_PICTURE_STUDENT_MALE),
        );
    }
}
