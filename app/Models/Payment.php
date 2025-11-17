<?php

namespace App\Models;

use App\Models\Scopes\YearScope;
use Illuminate\Database\Eloquent\Model;
/**
 * @property string $id
 * @property string $nombre
 * @property string $desc1
 * @property string $fecha_d
 * @property float $pago
 * @property string $year
 * @property string $fecha_p
 * @property float $deuda
 * @property int $codigo
 * @property string $ss
 * @property string $tdp
 * @property string $grado
 * @property int $code1
 * @property string $usuario
 * @property string $hora
 * @property string $fecha2
 * @property string $nuchk
 * @property string $paypal
 * @property int $rec
 * @property int $add1
 * @property int $id2
 * @property string $computadora
 * @property string $ip
 * @property int $mt
 * @property string $baja
 * @property int $bash
 * @property int $caja
 * @property string $razon
 * @property string $fecha_r
 * @property string $chkd
 * @property string $cdc
 * @property string $codigo2
 */
class Payment extends Model
{
    protected $table = 'pagos';
    protected $primaryKey = 'mt';
    public $timestamps = false;
    protected $guarded = [];
    protected static function booted(): void
    {
        static::addGlobalScope(new YearScope);
    }

    public static function nextReceiptNumber(): int
    {
        // Get the maximum receipt number from the payments table, ignoring the year scope
        $maxReceipt = self::withoutGlobalScope(YearScope::class)->max('rec');

        // If no receipts exist, start from 1, otherwise increment the max by 1
        return $maxReceipt ? $maxReceipt + 1 : 1;
    }
}
