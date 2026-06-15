<?php

namespace App\Models\Exam;

use App\Models\Student;
use App\Models\Subject;
use App\Models\Teacher;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @phpstan-type YesNo 'si'|'no'
 * 
 * @property int $id
 * @property int $id_maestro teachers's id
 * @property string $titulo
 * @property string $curso
 * @property int $valor
 * @property int $valor2
 * @property YesNo $ver_nota
 * @property CarbonInterface $fecha
 * @property string|null $hora
 * @property string $hora_final
 * @property YesNo $activo
 * @property YesNo $desc1
 * @property string|null $desc1_1
 * @property YesNo $desc2
 * @property string|null $desc2_1
 * @property YesNo $desc3
 * @property string|null $desc3_1
 * @property YesNo $desc4
 * @property string|null $desc4_1
 * @property YesNo $desc5
 * @property string|null $desc5_1
 * @property int $tiempo
 * @property-read Subject $subject
 * @property-read Teacher $teacher
 */
class Exam extends Model
{

    protected $table = 'T_examenes';

    protected $guarded = [];

    public $timestamps = false;

    public const string ACTIVE = 'si';
    public const string INACTIVE = 'no';


    /**
     * @return BelongsTo<Teacher, $this>
     */
    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class, 'id_maestro', 'id');
    }

    /**
     * @return BelongsTo<Subject, $this>
     */
    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class, 'curso', 'curso');
    }

    /**
     * @return HasMany<DoneExam, $this>
     */
    public function doneExams(): HasMany
    {
        return $this->hasMany(DoneExam::class, 'id_examen', 'id');
    }

    /**
     * @param Builder<$this> $query
     */
    protected function scopeActive(Builder $query): void
    {
        $query->where('activo', self::ACTIVE)->whereTodayOrAfter('fecha');
    }

    public function hasBeenTakenByStudent(Student $student): bool
    {
        return $this->doneExams()->where('id_estudiante', $student->mt)->exists();
    }

    /**
     * @return array<string,string>
     */
    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'id_maestro' => 'integer',
            'fecha' => 'date:Y-m-d',
        ];
    }
}
