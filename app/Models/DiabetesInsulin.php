<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class DiabetesInsulin
 * 
 * @property int $id
 * @property string $ss
 * @property string|null $rango
 * @property string|null $horas
 * @property string|null $ejer1
 * @property string|null $ejer2
 * @property string|null $hiper
 * @property string|null $hipo
 * @property string|null $otro
 * @property string|null $otro2
 * @property string|null $gluc1
 * @property string|null $gluc2
 * @property string|null $gluc3
 * @property string|null $exc1
 * @property string|null $exc2
 * @property string|null $gluc_med
 * @property string|null $ins1
 * @property string|null $ins1_n
 * @property string|null $ins1_u
 * @property string|null $ins2
 * @property string|null $ins2_n
 * @property string|null $ins2_u
 * @property string|null $ins3
 * @property string|null $ins3_n
 * @property string|null $ins3_u
 * @property string|null $insulina
 * @property string|null $insuni1-insuni15
 * @property string|null $insu1-insu9
 * 
 * @property-read Student $student
 */
class DiabetesInsulin extends Model
{
    protected $table = 'diabetes_insulina';
    
    protected $primaryKey = null;
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id',
        'ss',
        'rango',
        'horas',
        'ejer1',
        'ejer2',
        'hiper',
        'hipo',
        'otro',
        'otro2',
        'gluc1',
        'gluc2',
        'gluc3',
        'exc1',
        'exc2',
        'gluc_med',
        'ins1',
        'ins1_n',
        'ins1_u',
        'ins2',
        'ins2_n',
        'ins2_u',
        'ins3',
        'ins3_n',
        'ins3_u',
        'insulina',
        'insuni1',
        'insuni2',
        'insuni3',
        'insuni4',
        'insuni5',
        'insuni6',
        'insuni7',
        'insuni8',
        'insuni9',
        'insuni10',
        'insuni11',
        'insuni12',
        'insuni13',
        'insuni14',
        'insuni15',
        'insu1',
        'insu2',
        'insu3',
        'insu4',
        'insu5',
        'insu6',
        'insu7',
        'insu8',
        'insu9',
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
