<?php

namespace App\Models;

use App\Models\Traits\HasYear;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $id_tarea
 * @property int $id_estudiante
 * @property int $id_profesor
 * @property string $nombre_archivo
 * @property string $curso
 * @property string $nota
 * @property CarbonInterface $fecha
 * @property string $hora
 * @property string $year
 * @property CarbonInterface $fecha_descargado
 * @property string $hora_descargado
 * @property Teacher $teacher
 * @property Student $student
 * @property Homework $homework
 */
class DoneHomework extends Model
{
    use HasYear;

    protected $table = 'tareas_enviadas';
    public $timestamps = false;
    protected $guarded = [];

    /**
     * @return BelongsTo<Homework, $this>
     */
    public function homework(): BelongsTo
    {
        return $this->belongsTo(Homework::class, 'id_tarea', 'id');
    }

    /**
     * @return BelongsTo<Teacher, $this>
     */
    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class, 'id_profesor', 'id');
    }

    /**
     * @return BelongsTo<Student, $this>
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'id_estudiante', 'id');
    }

    /**
     * @param Builder<$this> $query
     */
    public function scopeOfClass(Builder $query, string $class): void
    {
        $query->where('curso', $class);
    }

    /**
     * @return array<string,string>
     */
    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'id_tarea' => 'integer',
            'id_estudiante' => 'integer',
            'id_profesor' => 'integer',
            'nombre_archivo' => 'string',
            'curso' => 'string',
            'nota' => 'string',
            'fecha' => 'date:Y-m-d',
            'hora' => 'string',
            'year' => 'string',
            'fecha_descargado' => 'date:Y-m-d',
            'hora_descargado' => 'string',
        ];
    }
}
