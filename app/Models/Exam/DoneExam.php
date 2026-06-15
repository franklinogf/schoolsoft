<?php

namespace App\Models\Exam;

use App\Models\Student;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\Traits\HasYear;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @phpstan-type YesNo 'si'|'no'
 * 
 * @property int $id
 * @property int $id_examen
 * @property int $id_estudiante student's id
 * @property string $ss_estudiante
 * @property string $curso
 * @property int $puntos
 * @property int $bonos
 * @property int $count
 * @property YesNo $oportunidad
 * @property CarbonInterface $terminado_el
 * @property string $year
 * @property-read Exam $exam
 * @property-read Subject $subject
 * @property-read Student $student
 */
class DoneExam extends Model
{
    use HasYear;

    protected $table = 'T_examenes_terminados';

    protected $guarded = [];

    public $timestamps = false;

    public const string ACTIVE = 'si';
    public const string INACTIVE = 'no';


    /**
     * @return BelongsTo<Exam, $this>
     */
    public function exam(): BelongsTo
    {
        return $this->belongsTo(Exam::class, 'id_examen', 'id');
    }

    /**
     * @return BelongsTo<Subject, $this>
     */
    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class, 'curso', 'curso');
    }

    /**
     * @return BelongsTo<Student, $this>
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'id_estudiante', 'mt');
    }

    /**
     * @return array<string,string>
     */
    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'id_maestro' => 'integer',
            'terminado_el' => 'date',
        ];
    }
}
