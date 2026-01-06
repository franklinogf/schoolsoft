<?php

namespace App\Models;

use App\Models\Scopes\YearScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property string $id
 * @property string $padre
 * @property string $madre
 * @property string $nombre
 * @property string $apellidos
 * @property string $descripcion
 * @property string $grado
 * @property string $curso
 * @property string $profesor
 * @property string $hora
 * @property string $dias
 * @property string $nota1
 * @property string $nota2
 * @property string $nota3
 * @property string $nota4
 * @property string $con1
 * @property string $con2
 * @property string $con3
 * @property string $con4
 * @property string $sem1
 * @property string $sem2
 * @property string $credito
 * @property string $final
 * @property string $usuario
 * @property string $clave
 * @property string $desc2
 * @property string $email
 * @property string $idioma
 * @property string $not1
 * @property string $not2
 * @property string $not3
 * @property string $not4
 * @property string $not5
 * @property string $not6
 * @property string $not7
 * @property string $not8
 * @property string $not9
 * @property string $not10
 * @property string $not11
 * @property string $not12
 * @property string $not13
 * @property string $not14
 * @property string $not15
 * @property string $not16
 * @property string $not17
 * @property string $not18
 * @property string $not19
 * @property string $not20
 * @property string $tpa1
 * @property string $tpa2
 * @property string $tpa3
 * @property string $tpa4
 * @property string $por1
 * @property string $por2
 * @property string $por3
 * @property string $por4
 * @property string $ss
 * @property string $not21
 * @property string $not22
 * @property string $not23
 * @property string $not24
 * @property string $not25
 * @property string $not26
 * @property string $not27
 * @property string $not28
 * @property string $not29
 * @property string $not30
 * @property string $not31
 * @property string $not32
 * @property string $not33
 * @property string $not34
 * @property string $not35
 * @property string $not36
 * @property string $not37
 * @property string $not38
 * @property string $not39
 * @property string $not40
 * @property string $letra
 * @property string $aus1
 * @property string $aus2
 * @property string $aus3
 * @property string $aus4
 * @property string $tar1
 * @property string $tar2
 * @property string $tar3
 * @property string $tar4
 * @property string $tl1
 * @property string $tl2
 * @property string $tl3
 * @property string $tl4
 * @property string $td1
 * @property string $td2
 * @property string $td3
 * @property string $td4
 * @property string $year
 * @property string $id2
 * @property string $nota_por
 * @property string $ptd1
 * @property string $ptd2
 * @property string $ptd3
 * @property string $ptd4
 * @property string $ptl1
 * @property string $ptl2
 * @property string $ptl3
 * @property string $ptl4
 * @property string $pal
 * @property string $sutri
 * @property string $sie1
 * @property string $sie2
 * @property string $sie3
 * @property string $sie4
 * @property string $sie5
 * @property string $sie6
 * @property string $sie7
 * @property string $sie8
 * @property string $pc1
 * @property string $pc2
 * @property string $pc3
 * @property string $pc4
 * @property string $tpc1
 * @property string $tpc2
 * @property string $tpc3
 * @property string $tpc4
 * @property string $control
 * @property string $ex1
 * @property string $ex2
 * @property string $de1
 * @property string $de2
 * @property string $de3
 * @property string $de4
 * @property string $com1
 * @property string $com2
 * @property string $com3
 * @property string $com4
 * @property string $verano
 * @property string $baja
 * @property int $orden
 * @property string $ava
 * @property float $valor
 * @property int $aa
 * @property int $average1
 * @property int $average2
 * @property int $average3
 * @property int $average4
 * @property float $peso
 * @property string $dip1
 * @property string $dip2
 * @property string $dip3
 * @property string $dip4
 * @property string $nta70
 * @property string $nta30
 */
class Classes extends Model
{
    protected $table = 'padres';
    protected $primaryKey = 'aa';
    public $timestamps = false;
    protected $guarded = [];

    protected static function booted(): void
    {
        static::addGlobalScope(new YearScope);
        static::addGlobalScope('lastName', function (Builder $builder) {
            $builder->orderBy('apellidos');
        });
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'ss', 'ss');
    }

    protected function scopeTable(Builder $query, string $table): void
    {
        $query->from($table);
    }
    
    protected function scopeOfClass(Builder $query, string $class): void
    {
        $query->where('curso', $class);
    }
}
