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
 * @property string|null $pp_token PlacetoPay card token (subscription), used by collect()
 * @property string|null $pp_subtoken
 * @property string|null $pp_last4
 * @property string|null $pp_brand
 * @property string|null $pp_nombre
 * @property string|null $pp_apellido
 * @property string $estado pendiente (draft)|activo|pausado|fallido
 * @property string|null $ultimo_cobro
 * @property int $intentos
 * @property string|null $proximo_intento
 * @property string|null $aviso_enviado
 * @property Collection<int, AutoPayItem> $items
 */
class AutoPay extends Model
{
    use HasYear;
    protected $table = 'posteos';
    public $timestamps = false;
    protected $guarded = [];

    public const string STATUS_PENDING = 'pendiente';
    public const string STATUS_ACTIVE = 'activo';
    public const string STATUS_FAILED = 'fallido';
    public const string STATUS_PAUSED = 'pausado';

    /** Rejected charges in a row before the auto pay is stopped. */
    public const int MAX_ATTEMPTS = 3;

    /**
     * Save the card token returned by an approved enrollment. A new card
     * repairs a failed auto pay (back to active, attempts reset) but never
     * activates a draft nor resumes a paused one: that is the parent's call.
     */
    public function storeCardToken(string $token, ?string $subtoken, ?string $last4 = null, ?string $brand = null): void
    {
        $this->update([
            'pp_token' => $token,
            'pp_subtoken' => $subtoken,
            'pp_last4' => $last4,
            'pp_brand' => $brand,
            'estado' => $this->estado === self::STATUS_FAILED ? self::STATUS_ACTIVE : $this->estado,
            'intentos' => 0,
            'proximo_intento' => null,
        ]);
    }

    /**
     * "Visa ****0081", or a generic label when there is no card.
     */
    public function cardLabel(): string
    {
        return trim(($this->pp_brand ?: __('Tarjeta')) . ($this->pp_last4 ? " ****{$this->pp_last4}" : ''));
    }

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
