<?php

namespace App\Models;

use App\Enums\Gender;
use App\Models\Scopes\YearScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Carbon\CarbonInterface;

/**
 * @property string $ss
 * @property string $year
 * @property string $grado
 * @property string $nombre
 * @property string $apellidos
 * @property string $id
 * @property string $genero
 * @property string $rema
 * @property CarbonInterface $fecha
 * @property string $cta
 * @property string $alias
 * @property string $verano
 * @property string $clase_verano
 * @property string $fechagra
 * @property string $vivecon
 * @property string $activo
 * @property string $beca
 * @property string $desc_men
 * @property string $desc_mat
 * @property string $desc_otro1
 * @property string $desc_otro2
 * @property string $fecha_baja
 * @property string $mat_retenida
 * @property float $tr1
 * @property float $tr2
 * @property float $se1
 * @property float $tr3
 * @property float $tr4
 * @property float $se2
 * @property float $fin
 * @property string $crs
 * @property string $cn1
 * @property string $cn2
 * @property string $cns1
 * @property string $cn3
 * @property string $cn4
 * @property string $cns2
 * @property string $cnf
 * @property string $cursos
 * @property string $cel
 * @property string $comp
 * @property string $nuref
 * @property string $lugar_nac
 * @property string $imp1
 * @property string $imp2
 * @property string $imp3
 * @property string $imp4
 * @property string $enf1
 * @property string $enf2
 * @property string $enf3
 * @property string $enf4
 * @property string $med1
 * @property string $med2
 * @property string $med3
 * @property string $med4
 * @property string $rec1
 * @property string $rec2
 * @property string $rec3
 * @property string $rec4
 * @property string $medico
 * @property string $tel1
 * @property string $tel2
 * @property string $religion
 * @property string $iglesia
 * @property string $bau
 * @property string $com
 * @property string $con
 * @property string $fbau
 * @property string $fcom
 * @property string $fcon
 * @property string $desc1
 * @property string $desc2
 * @property string $desc3
 * @property string $desc4
 * @property string $nuevo
 * @property CarbonInterface $fecha_matri
 * @property int $codigobaja
 * @property int $edad
 * @property string $gra2
 * @property string $imagen
 * @property string $tipo
 * @property string $act2
 * @property string $usuario
 * @property string $clave
 * @property int $tipo_foro
 * @property int $mt
 * @property string $email
 * @property string $avatar
 * @property string $padre
 * @property string $nombre_padre
 * @property string $dir1
 * @property string $dir2
 * @property string $pueblo
 * @property string $estado
 * @property string $zip
 * @property string $colpro
 * @property string $cdb1
 * @property string $cdb2
 * @property string $cdb3
 * @property string $pop
 * @property string $celp
 * @property string $emailp
 * @property string $telp
 * @property string $id3
 * @property int $raza
 * @property int $rel
 * @property float $cantidad
 * @property float $cantidad_alerta
 * @property string $f_deposito
 * @property string $cbarra
 * @property string $avisar
 * @property int $transporte
 * @property string $municipio
 * @property string $acomodo
 * @property string $trajo
 * @property string $emaile
 * @property string $zona
 * @property string $ent
 * @property string $ip
 * @property string $datem
 * @property string $horam
 * @property float $tmat
 * @property string $matri
 * @property string $her
 * @property string $pago
 * @property string $feg2
 * @property string $feg
 * @property float $cuota
 * @property string $major
 * @property string $e_s
 * @property string $pago_e_s
 * @property string $hora_pago_e_s
 * @property float $pago_mensual
 * @property string $ip2
 * @property string $firma1
 * @property string $firma2
 * @property string $firma3
 * @property string $fecha_pago_e_s
 * @property string $id_ref
 * @property string $ent2
 * @property string $mes
 * @property string $hora1
 * @property string $hora2
 * @property string $hora3
 * @property string $fecha_e_s
 * @property string $matri2
 * @property string $pago_p_c
 * @property string $ent3
 * @property string $p_c
 * @property int $p_c_trxID
 * @property string $p_c_fecha
 * @property string $p_c_hora
 * @property string $re_ma
 * @property string $vera
 * @property string $mv1
 * @property string $mv2
 * @property string $formInternet
 * @property float $pm1
 * @property float $pm2
 * @property float $pm3
 * @property string $pm1_pago
 * @property string $pm2_pago
 * @property string $pm3_pago
 * @property string $pm1_fecha
 * @property string $pm2_fecha
 * @property string $pm3_fecha
 * @property string $pm1_trxID
 * @property string $pm2_trxID
 * @property string $pm3_trxID
 * @property string $pm1_hora
 * @property string $pm2_hora
 * @property string $pm3_hora
 * @property float $balance_a
 * @property string $pfoto
 * @property string $hde
 * @property string $codigopin
 * @property Collection<int, Classes> $classes
 * @property Family|null $family
 * @property StudentNeed|null $needs
 * @property Infirmary|null $infirmary
 */
class Student extends Model
{
    protected $table = 'year';
    protected $primaryKey = 'mt';
    public $timestamps = false;
    protected $guarded = [];

    protected static function booted(): void
    {
        static::addGlobalScope(new YearScope);

        static::addGlobalScope('lastName', function (Builder $builder) {
            $builder->orderBy('apellidos');
        });

        static::addGlobalScope('unenrolled', function (Builder $builder) {
            $builder->where('codigobaja', '0');
        });
    }

    public function casts(): array
    {
        return [
            'fecha' => 'date:Y-m-d',
            'fecha_matri' => 'date:Y-m-d',
            // 'genero' => Gender::class,
        ];
    }

    public function classes(): HasMany
    {
        return $this->hasMany(Classes::class, 'ss', 'ss')->orderBy('curso');
    }


    protected function scopeWithUnerolled(Builder $query): void
    {
        $query->withoutGlobalScope('unenrolled');
    }

    protected function scopeByGrade(Builder $query, string $grade): void
    {
        $query->where('grado', $grade)->orderBy('grado');
    }

    public function family(): BelongsTo
    {
        return $this->belongsTo(Family::class, 'id', 'id');
    }

    protected function scopeById(Builder $query, int|string $id): void
    {
        $query->where('id', $id);
    }

    protected function scopeByMT(Builder $query, int|string $mt): void
    {
        $query->where('mt', $mt);
    }

    protected function scopeBySS(Builder $query, string $ss): void
    {
        $query->where('ss', $ss);
    }

    protected function scopeByClass(Builder $query, string $class, string $table = 'padres', bool $isSummer = false): void
    {
        $query->whereHas('classes', function (Builder $q) use ($class, $table, $isSummer) {
            $q->from($table)
                ->where('curso', $class);
            if ($isSummer) {
                $q->where('verano', 'Si');
            }
        });
    }

    protected function fullName(): Attribute
    {
        return Attribute::make(
            get: fn($value, array $attributes) =>
            $attributes['nombre'] . ' ' . $attributes['apellidos'],
        );
    }
    protected function reversedFullName(): Attribute
    {

        return Attribute::make(
            get: fn($value, array $attributes) =>
            $attributes['apellidos'] . ' ' . $attributes['nombre'],
        );
    }

    protected function profilePicture(): Attribute
    {
        return Attribute::make(
            get: fn($value, array $attributes) =>
            $attributes['imagen'] !== '' ? school_asset(__STUDENT_PROFILE_PICTURE_PATH . $attributes['imagen'])
                : ($attributes['genero'] === Gender::FEMALE->value
                    ? __NO_PROFILE_PICTURE_STUDENT_FEMALE
                    : __NO_PROFILE_PICTURE_STUDENT_MALE),
        );
    }

    public function needs(): HasOne
    {
        return $this->hasOne(StudentNeed::class, 'ss', 'ss');
    }

    public function infirmary(): HasOne
    {
        return $this->hasOne(Infirmary::class, 'ss', 'ss');
    }

    public function vitals(): HasMany
    {
        return $this->hasMany(Vital::class, 'ss', 'ss');
    }

    public function infirmaryVisits(): HasMany
    {
        return $this->hasMany(InfirmaryVisit::class, 'ss', 'ss');
    }

    public function vaccineExemptions(): HasMany
    {
        return $this->hasMany(VaccineExemption::class, 'ss', 'ss');
    }

    public function infirmaryCertifications(): HasMany
    {
        return $this->hasMany(InfirmaryCertification::class, 'ss', 'ss');
    }

    public function incompleteVaccines(): HasMany
    {
        return $this->hasMany(IncompleteVaccine::class, 'ss', 'ss');
    }

    public function diabetesInfo(): HasOne
    {
        return $this->hasOne(DiabetesInfo::class, 'ss', 'ss');
    }

    public function diabetesExercise(): HasOne
    {
        return $this->hasOne(DiabetesExercise::class, 'ss', 'ss');
    }

    public function diabetesInsulin(): HasOne
    {
        return $this->hasOne(DiabetesInsulin::class, 'ss', 'ss');
    }

    public function diabetesInsulinPump(): HasOne
    {
        return $this->hasOne(DiabetesInsulinPump::class, 'ss', 'ss');
    }
}
