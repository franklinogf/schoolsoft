<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class DiabetesExercise
 * 
 * @property int $id
 * @property string $ss
 * @property string|null $carb
 * @property string|null $actividad
 * @property string|null $glucosa_min
 * @property string|null $glucosa_max
 * @property string|null $sintomas_hipo
 * @property string|null $tratamiento_hipo
 * @property string|null $dosis
 * @property string|null $sintomas_hiper
 * @property string|null $tratamiento_hiper
 * @property string|null $azucar
 * 
 * @property-read Student $student
 */
class DiabetesExercise extends Model
{
    protected $table = 'diabetes_ejercicios';
    
    protected $primaryKey = null;
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id',
        'ss',
        'carb',
        'actividad',
        'glucosa_min',
        'glucosa_max',
        'sintomas_hipo',
        'tratamiento_hipo',
        'dosis',
        'sintomas_hiper',
        'tratamiento_hiper',
        'azucar',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'ss', 'ss');
    }

    public static function findByStudent(string $id, string $ss): ?self
    {
        return self::where('id', $id)->where('ss', $ss)->first();
    }

    public static function updateOrCreateRecord(string $id, string $ss, array $data): self
    {
        return self::updateOrCreate(['id' => $id, 'ss' => $ss], $data);
    }
}
