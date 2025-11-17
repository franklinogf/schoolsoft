<?php

namespace App\Models;

use App\Models\Scopes\YearScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property string $year
 * @property string $asignatura
 * @property int $grado
 * @property string $fecha
 * @property string $desde
 * @property string $hasta
 * @property string $tema
 * @property string $estandares1
 * @property string $estandares2
 * @property string $estandares3
 * @property string $objetivos1
 * @property string $objetivos2
 * @property string $objetivos3
 * @property string $objetivos4
 * @property string $destrezas1
 * @property string $destrezas2
 * @property string $destrezas3
 * @property string $destrezas4
 * @property string $estand_comun1
 * @property string $estand_comun2
 * @property string $estand_comun3
 * @property string $estand_comun4
 * @property string $apoyo1 - apoyo27
 * @property string $integracion1 - integracion12
 * @property string $estrategias1 - estrategias15
 * @property string $portafolio1
 * @property string $portafolio2
 * @property string $prueba1
 * @property string $prueba2
 * @property string $proyecto1
 * @property string $proyecto2
 * @property string $contestar1
 * @property string $contestar2
 * @property string $valores1 - valores22
 * @property string $acomodo1 - acomodo6
 * @property string $acomodo1_1 - acomodo6_2
 * @property string $semanal1_1 - semanal10_3
 * @property string $revisado1 - revisado5
 * @property int $id2
 * @property Teacher|null $teacher
 */
class WeeklyPlan2 extends Model
{
    protected $table = 'plansemanal2';
    protected $primaryKey = 'id2';
    public $timestamps = false;
    protected $guarded = [];

    protected static function booted(): void
    {
        static::addGlobalScope(new YearScope);
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class, 'id', 'id');
    }

    public function scopeByTeacher(Builder $query, int $teacherId): Builder
    {
        return $query->where('id', $teacherId);
    }

    public function scopeByGrade(Builder $query, string $grade): Builder
    {
        return $query->where('grado', $grade);
    }

    public function scopeBySubject(Builder $query, string $asignatura): Builder
    {
        return $query->where('asignatura', $asignatura);
    }
}
