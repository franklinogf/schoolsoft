<?php

namespace App\Models\Foro;

use App\Models\Student;
use App\Models\Teacher;
use App\Models\Traits\HasYear;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $creador_id creator's id
 * @property int $entrada_id topic's id
 * @property string $tipo created's type (p = Teacher model)
 * @property string $descripcion
 * @property CarbonInterface $fecha
 * @property string $hora
 * @property string $year
 * @property-read Teacher $teacher
 * @property-read Student $student
 * @property-read Topic $topic
 */
class TopicComment extends Model
{
    use HasYear;
    protected $table = 'detalle_foro_entradas';

    protected $guarded = [];

    public $timestamps = false;

    public const string TEACHER_TYPE = 'p';
    public const string STUDENT_TYPE = 'e';

    protected static function booted(): void
    {
        static::creating(function (): void {
            dd('creating');
        });
    }

    /**
     * @return BelongsTo<Teacher, $this>
     */
    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class, 'creador_id', 'id');
    }

    /**
     * @return BelongsTo<Student, $this>
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'creador_id', 'id');
    }

    /**
     * @return BelongsTo<Topic, $this>
     */
    public function topic(): BelongsTo
    {
        return $this->belongsTo(Topic::class, 'entrada_id', 'id');
    }

    /**
     * @return array<string,string>
     */
    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'creador_id' => 'integer',
            'entrada_id' => 'integer',
            'tipo' => 'string',
            'descripcion' => 'string',
            'fecha' => 'date:Y-m-d',
            'hora' => 'string',
            'year' => 'string',
        ];
    }
}
