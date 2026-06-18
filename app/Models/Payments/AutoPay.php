<?php

namespace App\Models\Payments;

use App\Enums\AchAccountTypeEnum;
use App\Enums\AutoPayPaymentEnum;
use App\Enums\AutoPayTypeEnum;
use App\Models\Traits\HasYear;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $cuenta
 * @property string $year
 * @property string $email
 * @property AutoPayTypeEnum $tipoDePago
 * @property string|null $ccNombre
 * @property int|null $ccNumero
 * @property string|null $fechaExpiracion
 * @property int|null $ccv
 * @property int|null $ccZip
 * @property string|null $achNombre
 * @property int|null $achNumero
 * @property int|null $numeroRuta
 * @property AchAccountTypeEnum|null $tipoCuenta (w = Cheques, s = Ahorros )
 * @property int|null $achZip
 * @property float $total
 * @property AutoPayPaymentEnum $formaDePago
 * @property int $diaDePago
 * @property Collection<int, AutoPayItem> $items
 */
class AutoPay extends Model
{
    use HasYear;
    protected $table = 'posteos';
    public $timestamps = false;
    protected $guarded = [];

    public function items(): HasMany
    {
        return $this->hasMany(AutoPayItem::class, 'posteoId', 'id');
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'tipoCuenta' => AchAccountTypeEnum::class,
            'tipoDePago' => AutoPayTypeEnum::class,
            'formaDePago' => AutoPayPaymentEnum::class,
            'total' => 'float',
        ];
    }
}
