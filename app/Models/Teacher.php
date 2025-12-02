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
use Illuminate\Support\Collection;

/**
 * @property int $id
 * @property string $nombre
 * @property string $apellidos
 * @property string $fullName
 * @property string $ss
 * @property string $tel_res
 * @property string $tel_emer
 * @property string $cel
 * @property string $posicion
 * @property string $genero
 * @property string $fecha_nac
 * @property string $fecha_ini
 * @property string $fecha_daja
 * @property string $nivel
 * @property string $preparacion1
 * @property string $preparacion2
 * @property string $grado
 * @property string $email1
 * @property string $email2
 * @property string $dir1
 * @property string $dir2
 * @property string $pueblo1
 * @property string $esta1
 * @property string $zip1
 * @property string $dir3
 * @property string $dir4
 * @property string $pueblo2
 * @property string $esta2
 * @property string $zip2
 * @property string $club1
 * @property string $club2
 * @property string $club3
 * @property string $club4
 * @property string $club5
 * @property string $usuario
 * @property string $clave
 * @property string $tipo
 * @property mixed $foto
 * @property string $grupo
 * @property string $activo
 * @property string $idioma
 * @property string $ufecha
 * @property string $re_e
 * @property string $year
 * @property string $cel_com
 * @property string $alias
 * @property string $baja
 * @property string $pre1
 * @property string $pre2
 * @property string $pre3
 * @property string $pre4
 * @property string $pre5
 * @property string $vi1
 * @property string $vi2
 * @property string $vi3
 * @property string $vi4
 * @property string $vi5
 * @property string $se1
 * @property string $se2
 * @property string $se3
 * @property string $se4
 * @property string $se5
 * @property string $comp
 * @property string $lic1
 * @property string $lic2
 * @property string $lic3
 * @property string $lic4
 * @property string $lp1
 * @property string $lp2
 * @property string $lp3
 * @property string $lp4
 * @property string $fex1
 * @property string $fex2
 * @property string $fex3
 * @property string $fex4
 * @property string $pe1
 * @property string $pe2
 * @property string $pe3
 * @property string $pe4
 * @property string $pe5
 * @property string $pe6
 * @property string $pe7
 * @property string $pe8
 * @property int $dep
 * @property string $dep_des
 * @property string $docente
 * @property string $foto_name
 * @property string $email_smtp
 * @property string $clave_email
 * @property string $host_smtp
 * @property int $port
 * @property string $host
 * @property int $tipo_foro
 * @property string $avatar
 * @property int $fechas
 * @property int $tri
 * @property string $pe9
 * @property string $pe10
 * @property string $pe11
 * @property string $pe12
 * @property string $pe13
 * @property string $pe14
 * @property string $pe15
 * @property string $pe16
 * @property string $cbarra
 * @property Collection<int, Subject> $subjects
 * @property Collection<int, Classes> $classes
 * @property Collection<int, Student> $homeStudents
 * @property Collection<int, WorkPlan> $workPlans
 * @property Collection<int, WorkPlan4> $workPlans4
 * @property Collection<int, WeeklyPlan> $weeklyPlans
 * @property Collection<int, ClassPlan> $classPlans
 * @property Collection<int, EnglishPlan> $englishPlans
 * @property Collection<int, UnitPlan> $unitPlans
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

    public function workPlans4(): HasMany
    {
        return $this->hasMany(WorkPlan4::class, 'id_profesor', 'id');
    }

    public function weeklyPlans(): HasMany
    {
        return $this->hasMany(WeeklyPlan::class, 'id', 'id');
    }
    public function weeklyPlans2(): HasMany
    {
        return $this->hasMany(WeeklyPlan2::class, 'id', 'id');
    }
    public function weeklyPlans3(): HasMany
    {
        return $this->hasMany(WeeklyPlan3::class, 'id_profesor', 'id');
    }

    public function classPlans(): HasMany
    {
        return $this->hasMany(ClassPlan::class, 'id_profesor', 'id');
    }

    public function englishPlans(): HasMany
    {
        return $this->hasMany(EnglishPlan::class, 'id_profesor', 'id');
    }

    public function englishLessonPlans(): HasMany
    {
        return $this->hasMany(EnglishLessonPlan::class, 'id_profesor', 'id');
    }

    public function unitPlans(): HasMany
    {
        return $this->hasMany(UnitPlan::class, 'id_profesor', 'id');
    }
}
