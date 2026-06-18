<?php

namespace App\Models\Payments;

use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $posteoId
 * @property string $mensaje
 * @property string $mes
 * @property string|null $descripcion
 * @property CarbonInterface $dateTime
 * @property string $authNumber
 * @property string $refNumber
 * @property AutoPay $autoPay
 */
class AutoPayHistory extends Model
{
    protected $table = 'posteos_historial';
    public $timestamps = false;
    protected $guarded = [];

    public function autoPay(): BelongsTo
    {
        return $this->belongsTo(AutoPay::class, 'posteoId', 'id');
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'dateTime' => 'datetime',
        ];
    }
}
