<?php

namespace App\Models;

use App\Enums\Gender;
use App\Models\Scopes\YearScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property int $id
 * @property string $usuario
 */
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

    protected function fullName(): Attribute
    {
        return Attribute::make(
            get: fn($value, array $attributes) => $attributes['nombre'] . ' ' . $attributes['apellidos'],
        );
    }

    public function subjects(): HasMany
    {
        return $this->hasMany(Subject::class, 'id', 'id');
    }

    public function classes(): HasMany
    {
        return $this->hasMany(Classes::class, 'id', 'id')->orderBy('curso');
    }

    public function homeStudents(): HasMany
    {
        return $this->hasMany(Student::class, 'grado', 'grado')
            ->where('fecha_baja', '0000-00-00')
            ->orderBy('apellidos');
    }

    public function workPlans(): HasMany
    {
        return $this->hasMany(WorkPlan::class, 'id', 'id');
    }
}
