<?php

namespace App\Models;

use App\Models\Scopes\YearScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property string $id
 * @property string $year
 * @property string $plan
 * @property string $grado
 * @property string $asignatura
 * @property string $mes
 * @property string $dia1
 * @property string $dia2
 * @property string $estandares
 * @property string $tema1
 * @property string $tema2
 * @property string $espectativas
 * @property string $np1
 * @property string $np2
 * @property string $np3
 * @property string $np4
 * @property string $np5
 * @property string $unidad
 * @property string $leccion
 * @property string $codigo
 * @property string $tema
 * @property string $pre1
 * @property string $pre2
 * @property string $pre3
 * @property string $obj1
 * @property string $obj2
 * @property string $obj3
 * @property string $integracion
 * @property string $act1
 * @property string $act2
 * @property string $act3
 * @property string $act4
 * @property string $ini1
 * @property string $ini2
 * @property string $ini3
 * @property string $ini4
 * @property string $ini5
 * @property string $ini6
 * @property string $ini7
 * @property string $des1
 * @property string $des2
 * @property string $des3
 * @property string $des4
 * @property string $des5
 * @property string $des6
 * @property string $des7
 * @property string $cie1
 * @property string $cie2
 * @property string $cie3
 * @property string $cie4
 * @property string $cie5
 * @property string $eva1
 * @property string $eva2
 * @property string $eva3
 * @property string $eva4
 * @property string $tab1
 * @property string $tab2
 * @property string $tab3
 * @property string $tab4
 * @property string $tab5
 * @property string $tab6
 * @property string $tab7
 * @property string $tab8
 * @property string $sel1
 * @property string $sel2
 * @property string $sel3
 * @property string $sel4
 * @property string $sel5
 * @property string $pro1
 * @property string $pro2
 * @property string $otro
 * @property string $as1
 * @property string $as2
 * @property string $as3
 * @property string $as4
 * @property string $as5
 * @property string $as6
 * @property string $as7
 * @property string $as8
 * @property string $autoeva
 * @property string $fecha
 * @property int $id2
 * @property string $tema3
 * @property string $tema4
 * @property string $tema5
 * @property string $ent1
 * @property string $ent2
 * @property string $ent3
 * @property string $ent4
 * @property string $ent5
 * @property string $ent6
 * @property string $ent7
 * @property string $ent8
 * @property string $ent9
 * @property string $ent10
 * @property string $ent11
 * @property string $ent12
 * @property string $ent13
 * @property string $ent14
 * @property string $ent15
 * @property string $ent16
 * @property string $ot1
 * @property string $ot2
 * @property string $ot3
 * @property string $ot4
 * @property string $ot5
 * @property string $ot6
 * @property string $otr1
 * @property string $otr2
 * @property string $otr3
 * @property string $otr4
 * @property string $otr5
 * @property string $otr6
 * @property string $otr7
 * @property string $otr8
 * @property Teacher|null $teacher
 */
class WorkPlan extends Model
{
    protected $table = 'plantrabajo';
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

    public function scopeBySubject(Builder $query, string $subject): Builder
    {
        return $query->where('asignatura', $subject);
    }
}
