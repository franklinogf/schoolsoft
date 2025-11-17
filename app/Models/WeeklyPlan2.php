<?php

namespace App\Models;

use App\Models\Scopes\YearScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property string $year
 * @property int $id2
 * @property string $asignatura
 * @property string $grado
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
 * @property string $apoyo1
 * @property string $apoyo2
 * @property string $integracion1
 * @property string $estrategias1
 * @property string $apoyo3
 * @property string $apoyo4
 * @property string $integracion2
 * @property string $estrategias2
 * @property string $apoyo5
 * @property string $apoyo6
 * @property string $integracion3
 * @property string $estrategias3
 * @property string $apoyo7
 * @property string $apoyo8
 * @property string $integracion4
 * @property string $estrategias4
 * @property string $apoyo9
 * @property string $apoyo10
 * @property string $integracion5
 * @property string $estrategias5
 * @property string $apoyo11
 * @property string $apoyo12
 * @property string $integracion6
 * @property string $estrategias6
 * @property string $apoyo13
 * @property string $apoyo14
 * @property string $integracion7
 * @property string $estrategias7
 * @property string $apoyo15
 * @property string $apoyo16
 * @property string $integracion8
 * @property string $estrategias8
 * @property string $apoyo17
 * @property string $apoyo18
 * @property string $integracion9
 * @property string $estrategias9
 * @property string $apoyo19
 * @property string $apoyo20
 * @property string $integracion10
 * @property string $estrategias10
 * @property string $apoyo21
 * @property string $apoyo22
 * @property string $integracion11
 * @property string $estrategias11
 * @property string $apoyo23
 * @property string $apoyo24
 * @property string $integracion12
 * @property string $estrategias12
 * @property string $apoyo25
 * @property string $apoyo26
 * @property string $estrategias13
 * @property string $apoyo27
 * @property string $estrategias14
 * @property string $estrategias15
 * @property string $portafolio1
 * @property string $prueba1
 * @property string $proyecto1
 * @property string $contestar1
 * @property string $portafolio2
 * @property string $prueba2
 * @property string $proyecto2
 * @property string $contestar2
 * @property string $valores1
 * @property string $valores2
 * @property string $valores3
 * @property string $valores4
 * @property string $valores5
 * @property string $valores6
 * @property string $valores7
 * @property string $valores8
 * @property string $valores9
 * @property string $valores10
 * @property string $valores11
 * @property string $valores12
 * @property string $valores13
 * @property string $valores14
 * @property string $valores15
 * @property string $valores16
 * @property string $valores17
 * @property string $valores18
 * @property string $valores19
 * @property string $valores20
 * @property string $valores21
 * @property string $valores22
 * @property string $acomodo1
 * @property string $acomodo2
 * @property string $acomodo3
 * @property string $acomodo4
 * @property string $acomodo5
 * @property string $acomodo6
 * @property string $acomodo1_1
 * @property string $acomodo1_2
 * @property string $acomodo2_1
 * @property string $acomodo2_2
 * @property string $acomodo3_1
 * @property string $acomodo3_2
 * @property string $acomodo4_1
 * @property string $acomodo4_2
 * @property string $acomodo5_1
 * @property string $acomodo5_2
 * @property string $acomodo6_1
 * @property string $acomodo6_2
 * @property string $semanal1_1
 * @property string $semanal1_2
 * @property string $semanal1_3
 * @property string $semanal2_1
 * @property string $semanal2_2
 * @property string $semanal2_3
 * @property string $semanal3_1
 * @property string $semanal3_2
 * @property string $semanal3_3
 * @property string $semanal4_1
 * @property string $semanal4_2
 * @property string $semanal4_3
 * @property string $semanal5_1
 * @property string $semanal5_2
 * @property string $semanal5_3
 * @property string $semanal6_1
 * @property string $semanal6_2
 * @property string $semanal6_3
 * @property string $semanal7_1
 * @property string $semanal7_2
 * @property string $semanal7_3
 * @property string $semanal8_1
 * @property string $semanal8_2
 * @property string $semanal8_3
 * @property string $semanal9_1
 * @property string $semanal9_2
 * @property string $semanal9_3
 * @property string $semanal10_1
 * @property string $semanal10_2
 * @property string $semanal10_3
 * @property string $revisado1
 * @property string $revisado2
 * @property string $revisado3
 * @property string $revisado4
 * @property string $revisado5
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
