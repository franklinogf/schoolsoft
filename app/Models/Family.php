<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Builder;
use App\Models\Traits\AlphaAndNumber;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $madre
 * @property string $padre
 * @property string $dir1
 * @property string $dir2
 * @property string $pueblo1
 * @property string $est1
 * @property string $zip1
 * @property string $dir3
 * @property string $dir4
 * @property string $pueblo2
 * @property string $est2
 * @property string $zip2
 * @property string $tel_m
 * @property string $tel_p
 * @property string $tel_t_m
 * @property string $tel_t_p
 * @property string $tel_e
 * @property string $email_m
 * @property string $email_p
 * @property string $ex_m
 * @property string $ex_p
 * @property string $usuario
 * @property string $clave
 * @property string $grupo
 * @property string $activo
 * @property string $cel_com_m
 * @property string $cel_com_p
 * @property string $cel_m
 * @property string $cel_p
 * @property string $trabajo_m
 * @property string $trabajo_p
 * @property string $posicion_m
 * @property string $posicion_p
 * @property string $re_e_m
 * @property string $re_e_p
 * @property string $re_mc_m
 * @property string $re_mc_p
 * @property string $ufecha
 * @property int $cta
 * @property float $sueldop
 * @property float $sueldom
 * @property string $year
 * @property string $tel_e2
 * @property int $nfam
 * @property string $qpaga
 * @property string $per1
 * @property string $per2
 * @property string $per3
 * @property string $per4
 * @property string $encargado
 * @property string $parentesco
 * @property string $tel_en
 * @property string $cel_e
 * @property string $com_e
 * @property string $dir_e1
 * @property string $dir_e2
 * @property string $pue_e
 * @property string $esta_e
 * @property string $zip_e
 * @property string $tel_t_e
 * @property string $rel1
 * @property string $rel2
 * @property string $rel3
 * @property string $rel4
 * @property string $tec1
 * @property string $tec2
 * @property string $tec3
 * @property string $tec4
 * @property string $tet1
 * @property string $tet2
 * @property string $tet3
 * @property string $tet4
 * @property string $cel1
 * @property string $cel2
 * @property string $cel3
 * @property string $cel4
 * @property string $email_e
 * @property string $ex_t_d_p
 * @property string $ex_t_d_m
 * @property string $fecha_p
 * @property string $promesa
 * @property string $fecha_e
 * @property string $codigop
 * @property string $codigom
 * @property string $salerta
 * @property string $alerta
 * @property string $fechk
 * @property string $obs
 * @property string $cantidad_acordada
 * @property string $tiempo_acordado
 * @property string $nuevos_cargos
 * @property string $total_pagar
 * @property string $per5
 * @property string $per6
 * @property string $rel5
 * @property string $rel6
 * @property string $tec5
 * @property string $tec6
 * @property string $tet5
 * @property string $tet6
 * @property string $cel5
 * @property string $cel6
 * @property string $id2
 * @property Collection<int, Student> $kids
 * @property Collection<int, Payment> $charges
 * @property Collection<int, Payment> $payments
 * @property Collection<int, Payment> $debts
 */
class Family extends Model
{
    use AlphaAndNumber;
    protected $table = 'madre';
    protected $primaryKey = 'id';
    public $timestamps = false;
    protected $guarded = [];

    public function kids(): HasMany
    {
        return $this->hasMany(Student::class, 'id', 'id');
    }

    public function charges(): HasMany
    {
        return $this->hasMany(Payment::class, 'id', 'id');
    }

    public function payments(): HasMany
    {
        return $this->charges()->whereDate('fecha_d', '!=', '0000-00-00');
    }

    public function debts(): HasMany
    {
        return $this->charges()->whereDate('fecha_d',  '0000-00-00');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->whereHas('kids');
    }
}
