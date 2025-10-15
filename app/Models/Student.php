<?php

namespace App\Models;

use App\Enums\Gender;
use App\Models\Scopes\YearScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property Family $family
 * @property int $mt
 * @property string $ss
 * @property string $nombre
 * @property string $apellidos
 * @property string $grado
 * @property string $genero
 * @property string $imagen
 * @property string $codigobaja
 * @property string $fecha
 * @property string $fecha_matri
 * @property string $fullName
 * @property string $reversedFullName
 * @property string $profilePicture
 * @property int $id
 * 
 */
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

        static::addGlobalScope('unenrolled', function (Builder $builder) {
            $builder->where('codigobaja', '0');
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


    public function scopeWithUnerolled(Builder $query): Builder
    {
        return $query->withoutGlobalScope('unenrolled');
    }

    public function scopeByGrade(Builder $query, string $grade): Builder
    {
        return $query->where('grado', $grade)->orderBy('grado');
    }

    public function family(): BelongsTo
    {
        return $this->belongsTo(Family::class, 'id', 'id');
    }



    public function scopeById(Builder $query, int|string $id): Builder
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
    protected function reversedFullName(): Attribute
    {

        return Attribute::make(
            get: fn($value, array $attributes) =>
            $attributes['apellidos'] . ' ' . $attributes['nombre'],
        );
    }

    protected function profilePicture(): Attribute
    {
        return Attribute::make(
            get: fn($value, array $attributes) =>
            $attributes['imagen'] !== '' ? school_asset(__STUDENT_PROFILE_PICTURE_PATH . $attributes['imagen'])
                : ($attributes['genero'] === Gender::FEMALE->value
                    ? __NO_PROFILE_PICTURE_STUDENT_FEMALE
                    : __NO_PROFILE_PICTURE_STUDENT_MALE),
        );
    }
}
