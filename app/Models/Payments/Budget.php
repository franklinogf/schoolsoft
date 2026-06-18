<?php

namespace App\Models\Payments;

use App\Models\Traits\HasYear;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $mt
 * @property int $codigo
 * @property string $descripcion
 * @property float $cantidad
 * @property float $costo
 * @property string $year
 */
class Budget extends Model
{
    use HasYear;
    protected $table = 'presupuesto';
    public $timestamps = false;
    protected $primaryKey = 'mt';
    protected $guarded = [];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'cantidad' => 'float',
            'costo' => 'float',
        ];
    }
}
