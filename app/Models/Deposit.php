<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id2
 * @property string $id family id (accountID)
 * @property string $ss
 * @property \Carbon\CarbonInterface $fecha
 * @property string|null $hora
 * @property float $cantidad
 * @property string $year
 * @property string $grado
 * @property int|null $studentId
 * @property string|null $email
 * @property string|null $descripcion
 * @property string|null $autorizacion
 * @property string|null $referencia
 * @property string|null $tarjetaUltimosDigitos
 * @property string|null $zip
 * @property string|null $tipoDePago
 * @property string|null $nombreEnLaTarjeta
 * @property string $otros
 * @property \Carbon\CarbonInterface $date
 * @property Student|null $student
 */
class Deposit extends Model
{
    protected $table = 'depositos';

    protected $primaryKey = 'id2';

    protected $guarded = ['id2'];

    public $timestamps = false;

    protected function casts(): array
    {
        return [
            'fecha' => 'date',
            'date' => 'datetime',
            'cantidad' => 'float',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'studentId', 'mt');
    }
}
