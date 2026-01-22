<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

/**
 * Class DiabetesInfo
 * 
 * @property int $id
 * @property string $ss
 * @property Carbon|null $fecha1
 * @property Carbon|null $fecha2
 * @property Carbon|null $fecha3
 * @property string|null $diabetes
 * @property string|null $doctor
 * @property string|null $direccion
 * @property string|null $calle
 * @property string|null $pueblo
 * @property string|null $postal
 * @property string|null $tel_doc
 * @property string|null $tel_emer
 * @property string|null $notificacion
 * 
 * @property-read Student $student
 */
class DiabetesInfo extends Model
{
    protected $table = 'diabetes';
    
    protected $primaryKey = null;
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id',
        'ss',
        'fecha1',
        'fecha2',
        'fecha3',
        'diabetes',
        'doctor',
        'direccion',
        'calle',
        'pueblo',
        'postal',
        'tel_doc',
        'tel_emer',
        'notificacion',
    ];

    protected $casts = [
        'fecha1' => 'date',
        'fecha2' => 'date',
        'fecha3' => 'date',
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
