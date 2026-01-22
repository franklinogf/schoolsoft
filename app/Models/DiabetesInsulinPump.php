<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class DiabetesInsulinPump
 * 
 * @property int $id
 * @property string $ss
 * @property string|null $tbomba
 * @property string|null $basal1-basal11
 * @property string|null $tinsulina
 * @property string|null $infusion
 * @property string|null $equipo
 * @property string|null $racion
 * @property string|null $factor
 * @property string|null $carb
 * @property string|null $bcarb
 * @property string|null $bcorrec
 * @property string|null $pbasales
 * @property string|null $btemp
 * @property string|null $dbomba
 * @property string|null $einfu
 * @property string|null $tubos
 * @property string|null $intinf
 * @property string|null $alarmas
 * @property string|null $med
 * @property string|null $hmed
 * @property string|null $omed
 * @property string|null $ohmed
 * 
 * @property-read Student $student
 */
class DiabetesInsulinPump extends Model
{
    protected $table = 'diabetes_insulina2';
    
    protected $primaryKey = null;
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id',
        'ss',
        'tbomba',
        'basal1',
        'basal2',
        'basal3',
        'basal4',
        'basal5',
        'basal6',
        'basal7',
        'basal8',
        'basal9',
        'basal10',
        'basal11',
        'tinsulina',
        'infusion',
        'equipo',
        'racion',
        'factor',
        'carb',
        'bcarb',
        'bcorrec',
        'pbasales',
        'btemp',
        'dbomba',
        'einfu',
        'tubos',
        'intinf',
        'alarmas',
        'med',
        'hmed',
        'omed',
        'ohmed',
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
