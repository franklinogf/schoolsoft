<?php

namespace App\Models;

use App\Models\Scopes\YearScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property string $year
 * @property string $clase
 * @property int $grado
 * @property string $tema
 * @property string $fecha
 * @property string $leccion
 * @property string $est
 * @property string $exp
 * @property string $obj_gen
 * @property string $nivel1
 * @property string $nivel2
 * @property string $nivel3
 * @property string $nivel4
 * @property string $lst_v1
 * @property string $lst_v2
 * @property string $lst_v3
 * @property string $lst_v4
 * @property string $act1
 * @property string $act2
 * @property string $act3
 * @property string $act4
 * @property string $act5
 * @property string $mat1
 * @property string $mat2
 * @property string $mat3
 * @property string $mat4
 * @property string $mat5
 * @property string $ini1
 * @property string $ini2
 * @property string $ini3
 * @property string $ini4
 * @property string $ini5
 * @property string $des1
 * @property string $des2
 * @property string $des3
 * @property string $des4
 * @property string $des5
 * @property string $cie1
 * @property string $cie2
 * @property string $cie3
 * @property string $cie4
 * @property string $cie5
 * @property string $asse1
 * @property string $asse2
 * @property string $asse3
 * @property string $asse4
 * @property string $asse5
 * @property string $coment
 * @property string $otros_m1
 * @property string $otros_m2
 * @property string $otros_m3
 * @property string $otros_m4
 * @property string $otros_m5
 * @property string $otros_i1
 * @property string $otros_i2
 * @property string $otros_i3
 * @property string $otros_i4
 * @property string $otros_i5
 * @property string $otros_d1
 * @property string $otros_d2
 * @property string $otros_d3
 * @property string $otros_d4
 * @property string $otros_d5
 * @property string $otros_c1
 * @property string $otros_c2
 * @property string $otros_c3
 * @property string $otros_c4
 * @property string $otros_c5
 * @property string $otros_a1
 * @property string $otros_a2
 * @property string $otros_a3
 * @property string $otros_a4
 * @property string $otros_a5
 * @property int $id2
 * @property Teacher|null $teacher
 */
class WeeklyPlan extends Model
{
    protected $table = 'plansemanal';
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

    public function scopeBySubject(Builder $query, string $clase): Builder
    {
        return $query->where('clase', $clase);
    }

    /**
     * Helper method to combine material values into comma-separated string
     */
    public static function combineMaterials(array $materials): string
    {
        return implode(',', array_filter($materials, fn($val) => !empty($val)));
    }

    /**
     * Helper method to split comma-separated string into array
     */
    public function getMaterialsArray(string $fieldName): array
    {
        return !empty($this->$fieldName) ? explode(',', $this->$fieldName) : [];
    }
}
