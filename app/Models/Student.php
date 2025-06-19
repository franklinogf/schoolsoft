<?php

namespace App\Models;

use App\Enums\Gender;
use App\Models\Scopes\YearScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Student extends Model
{
    protected $table = 'year';
    protected $primaryKey = 'mt';
    public $timestamps = false;
    protected $guarded = [];

    protected static function booted(): void
    {
        static::addGlobalScope(new YearScope);

        static::addGlobalScope('lastName', function (Builder $builder) {
            $builder->orderBy('apellidos');
        });
    }

    public function casts(): array
    {
        return [
            'fecha' => 'date:Y-m-d',
            'fecha_matri' => 'date:Y-m-d',
            // 'genero' => Gender::class,
        ];
    }

    public function classes(): HasMany
    {
        return $this->hasMany(Classes::class, 'ss', 'ss')->orderBy('curso');
    }


    public function scopeUnerolled(Builder $query): Builder
    {
        return $query->where('codigobaja', '0');
    }

    public function scopeByGrade(Builder $query, string $grade): Builder
    {
        return $query->where('grado', $grade)->orderBy('grado');
    }

    public function family(): BelongsTo
    {
        return $this->belongsTo(Family::class, 'id', 'id');
    }



    public function scopeById(Builder $query, int $id): Builder
    {
        return $query->where('id', $id);
    }
    public function scopeBySS(Builder $query, string $ss): Builder
    {
        return $query->where('ss', $ss);
    }

    protected function fullName(): Attribute
    {
        return Attribute::make(
            get: fn($value, array $attributes) =>
            $attributes['nombre'] . ' ' . $attributes['apellidos'],
        );
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
