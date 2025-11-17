<?php

namespace App\Models;

use App\Models\Scopes\YearScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $id_profesor
 * @property string $unidad
 * @property string $temas
 * @property string $fecha1
 * @property string $fecha2
 * @property string $fecha3
 * @property string $fecha4
 * @property string $fecha5
 * @property string $fase1
 * @property string $fase2
 * @property string $fase3
 * @property string $niveles1
 * @property string $niveles2
 * @property string $niveles3
 * @property string $niveles4
 * @property string $estandares1
 * @property string $estandares2
 * @property string $estandares3
 * @property string $estandares4
 * @property string $expectativas1
 * @property string $expectativas2
 * @property string $expectativas3
 * @property string $expectativas4
 * @property string $expectativas5
 * @property string $conceptual
 * @property string $procedimental
 * @property string $actitudinal
 * @property string $avaluo1
 * @property string $avaluo2
 * @property string $avaluo3
 * @property string $avaluo4
 * @property string $avaluo5
 * @property string $avaluo6
 * @property string $avaluo7
 * @property string $avaluo8
 * @property string $avaluo9
 * @property string $avaluo10
 * @property string $avaluo11
 * @property string $avaluo12
 * @property string $avaluo13
 * @property string $avaluo14
 * @property string $avaluo15
 * @property string $avaluo16
 * @property string $avaluo17
 * @property string $avaluo18
 * @property string $avaluo19
 * @property string $avaluo20
 * @property string $avaluo201
 * @property string $comprension1
 * @property string $comprension2
 * @property string $comprension3
 * @property string $comprension4
 * @property string $aprendizaje
 * @property string $aprendizaje_problema
 * @property string $integracion1
 * @property string $integracion2
 * @property string $integracion3
 * @property string $integracion4
 * @property string $integracion5
 * @property string $integracion6
 * @property string $integracion7
 * @property string $integracion8
 * @property string $integracion81
 * @property string $inicio1
 * @property string $inicio2
 * @property string $inicio3
 * @property string $inicio4
 * @property string $inicio5
 * @property string $inicio6
 * @property string $inicio7
 * @property string $inicio8
 * @property string $inicio9
 * @property string $inicio10
 * @property string $inicio11
 * @property string $inicio12
 * @property string $inicio121
 * @property string $desarrollo1
 * @property string $desarrollo2
 * @property string $desarrollo3
 * @property string $desarrollo4
 * @property string $desarrollo5
 * @property string $desarrollo6
 * @property string $desarrollo7
 * @property string $desarrollo8
 * @property string $desarrollo9
 * @property string $desarrollo10
 * @property string $desarrollo11
 * @property string $desarrollo12
 * @property string $desarrollo121
 * @property string $cierre1
 * @property string $cierre2
 * @property string $cierre3
 * @property string $cierre4
 * @property string $cierre5
 * @property string $cierre6
 * @property string $cierre7
 * @property string $cierre8
 * @property string $cierre81
 * @property string $acomodo1
 * @property string $acomodo2
 * @property string $acomodo3
 * @property string $acomodo4
 * @property string $acomodo5
 * @property string $acomodo6
 * @property string $acomodo61
 * @property string $materiales1
 * @property string $materiales2
 * @property string $materiales3
 * @property string $materiales4
 * @property string $materiales5
 * @property string $materiales6
 * @property string $materiales7
 * @property string $materiales8
 * @property string $materiales9
 * @property string $materiales10
 * @property string $materiales11
 * @property string $materiales12
 * @property string $materiales13
 * @property string $materiales14
 * @property string $materiales15
 * @property string $materiales16
 * @property string $materiales17
 * @property string $materiales171
 * @property string $year
 * @property Teacher|null $teacher
 */
class WorkPlan4 extends Model
{
    protected $table = 'plan_trabajo4';
    protected $primaryKey = 'id';
    public $timestamps = false;
    protected $guarded = [];

    protected static function booted(): void
    {
        static::addGlobalScope(new YearScope);
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class, 'id_profesor', 'id');
    }

    public function scopeByTeacher(Builder $query, int $teacherId): Builder
    {
        return $query->where('id_profesor', $teacherId);
    }
}
