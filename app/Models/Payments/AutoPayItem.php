<?php

namespace App\Models\Payments;

use App\Models\Student;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $posteoId
 * @property int $estudianteId
 * @property int $presupuesto budget code
 * @property float $cantidad
 * @property AutoPay $autoPay
 * @property Student $student
 * @property Budget $budget
 */
class AutoPayItem extends Model
{
    protected $table = 'posteos_detalles';
    public $timestamps = false;
    protected $guarded = [];

    /**
     * @return BelongsTo<AutoPay, $this>
     */
    public function autoPay(): BelongsTo
    {
        return $this->belongsTo(AutoPay::class, 'posteoId', 'id');
    }

    /**
     * @return BelongsTo<Student, $this>
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'estudianteId', 'mt');
    }

    /**
     * @return BelongsTo<Budget, $this>
     */
    public function budget(): BelongsTo
    {
        return $this->belongsTo(Budget::class, 'presupuesto', 'codigo');
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'cantidad' => 'float',
        ];
    }
}
