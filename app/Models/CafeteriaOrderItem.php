<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


/**
 * @property int $id
 * @property int $id_compra
 * @property string $descripcion
 * @property float $precio
 * @property float $precio_final
 * @property int $id_inv
 * @property int $id_boton
 * @property string $cbarra
 * @property string $fecha
 * @property string $ss
 * @property string $year
 * @property string $cn
 * @property CafeteriaOrder|null $order
 */
class CafeteriaOrderItem extends Model
{
    protected $table = "compra_cafeteria_detalle";
    public $timestamps = false;
    protected $guarded = [];

    public function order(): BelongsTo
    {
        return $this->belongsTo(CafeteriaOrder::class, "id_compra");
    }
}
