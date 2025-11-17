<?php

namespace App\Models;

use App\Models\Scopes\YearScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

/**
 * @property int $id
 * @property string $nombre
 * @property string $apellido
 * @property string $ss
 * @property string $grado
 * @property string $fecha
 * @property int $tdp
 * @property string $year
 * @property int $id2
 * @property float $total
 * @property float $pago1
 * @property float $pago2
 * @property string $tdp2
 * @property string $cn
 * @property string $hora
 * @property float $balance
 * @property mixed $receipt_sent
 * @property string $sent_at
 * @property string $failed_reason
 * @property Collection<int, CafeteriaOrderItem> $items
 * @property Student|null $buyer
 */
class CafeteriaOrder extends Model
{
    protected $table = "compra_cafeteria";
    public $timestamps = false;
    protected $guarded = [];

    protected static function booted(): void
    {
        static::addGlobalScope(new YearScope);
    }
    public function items(): HasMany
    {
        return $this->hasMany(CafeteriaOrderItem::class, 'id_compra');
    }

    public function buyer()
    {
        return $this->belongsTo(Student::class, 'ss', 'ss');
    }
}
