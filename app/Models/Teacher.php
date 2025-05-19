<?php

namespace App\Models;

use App\Enums\Gender;
use App\Models\Scopes\YearScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Teacher extends Model
{
    protected $table = 'profesor';
    protected $primaryKey = 'id';
    public $timestamps = false;
    protected $guarded = [];

    public function scopeByGrade(Builder $query, string $grade): Builder
    {
        return $query->where('grado', $grade);
    }
    protected function profilePicture(): Attribute
    {
        return Attribute::make(
            get: fn($value, array $attributes) =>
            $attributes['foto_name'] !== '' ? school_asset(__STUDENT_PROFILE_PICTURE_URL . $attributes['foto_name'])
                : ($attributes['genero'] === Gender::FEMALE->value
                    ? __NO_PROFILE_PICTURE_STUDENT_FEMALE
                    : __NO_PROFILE_PICTURE_STUDENT_MALE),
        );
    }
}
