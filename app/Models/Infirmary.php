<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $ss
 * @property string $va_dia
 * @property string $vac1
 * @property string $vac2
 * @property string $vac3
 * @property string $vac4
 * @property string $vac5
 * @property string $vac6
 * @property string $refuerzos
 * @property int $peso
 * @property string $estatura
 * @property string $cond_salud
 * @property string $med_usodi
 * @property string $dosis
 * @property string $frec
 * @property string $comportamiento
 * @property string $piel1
 * @property string $piel2
 * @property string $piel3
 * @property string $piel4
 * @property string $piel5
 * @property string $cicatrices
 * @property string $quemaduras
 * @property string $vision
 * @property string $audicion
 * @property string $nasal
 * @property string $espejuelos
 * @property string $dentadura
 * @property string $respiracion
 * @property string $asma
 * @property string $condritis
 * @property string $espasmos
 * @property string $ab_edema
 * @property string $ab_herida
 * @property string $ab_deformidad
 * @property string $desc1
 * @property string $ex_edema
 * @property string $ex_herida
 * @property string $ex_deformidad
 * @property string $ex_protesis
 * @property string $desc2
 * @property string $historial
 * @property string $com1
 * @property string $com2
 * @property string $com3
 * @property string $com4
 * @property string $vac7
 * @property string $vac8
 * @property string $vac9
 * @property string $vac10
 * @property string $vac11
 * @property string $vac12
 * @property string $vac13
 * @property string $vac14
 * @property string $vac15
 * @property string $vac16
 * @property string $vac17
 * @property string $vac18
 * @property Student|null $student
 */
class Infirmary extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'enfermeria';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'ss';

    /**
     * The "type" of the primary key ID.
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    protected $guarded = [];

    /**
     * Get the student that owns the infirmary record.
     */
    public function student()
    {
        return $this->belongsTo(Student::class, 'ss', 'ss');
    }

    /**
     * Parse the family health history from the concatenated string format.
     * Legacy format: "Si-Si---No-Si-..." (45 checkboxes separated by dashes)
     * 
     * @return array Associative array with disease categories and family member flags
     */
    public function getFamilyHistoryArray(): array
    {
        if (empty($this->historial)) {
            return [
                'heart_disease' => ['student' => '', 'father' => '', 'mother' => '', 'sibling' => '', 'other' => ''],
                'cancer' => ['student' => '', 'father' => '', 'mother' => '', 'sibling' => '', 'other' => ''],
                'diabetes' => ['student' => '', 'father' => '', 'mother' => '', 'sibling' => '', 'other' => ''],
                'hypertension' => ['student' => '', 'father' => '', 'mother' => '', 'sibling' => '', 'other' => ''],
                'hypotension' => ['student' => '', 'father' => '', 'mother' => '', 'sibling' => '', 'other' => ''],
                'hyperthyroidism' => ['student' => '', 'father' => '', 'mother' => '', 'sibling' => '', 'other' => ''],
                'hypothyroidism' => ['student' => '', 'father' => '', 'mother' => '', 'sibling' => '', 'other' => ''],
                'anemia' => ['student' => '', 'father' => '', 'mother' => '', 'sibling' => '', 'other' => ''],
                'other' => ['student' => '', 'father' => '', 'mother' => '', 'sibling' => '', 'other' => ''],
            ];
        }

        $checkboxes = explode('-', $this->historial);
        
        // Ensure we have exactly 45 elements
        $checkboxes = array_pad($checkboxes, 45, '');

        return [
            'heart_disease' => [
                'student' => $checkboxes[0] ?? '',
                'father' => $checkboxes[1] ?? '',
                'mother' => $checkboxes[2] ?? '',
                'sibling' => $checkboxes[3] ?? '',
                'other' => $checkboxes[4] ?? '',
            ],
            'cancer' => [
                'student' => $checkboxes[5] ?? '',
                'father' => $checkboxes[6] ?? '',
                'mother' => $checkboxes[7] ?? '',
                'sibling' => $checkboxes[8] ?? '',
                'other' => $checkboxes[9] ?? '',
            ],
            'diabetes' => [
                'student' => $checkboxes[10] ?? '',
                'father' => $checkboxes[11] ?? '',
                'mother' => $checkboxes[12] ?? '',
                'sibling' => $checkboxes[13] ?? '',
                'other' => $checkboxes[14] ?? '',
            ],
            'hypertension' => [
                'student' => $checkboxes[15] ?? '',
                'father' => $checkboxes[16] ?? '',
                'mother' => $checkboxes[17] ?? '',
                'sibling' => $checkboxes[18] ?? '',
                'other' => $checkboxes[19] ?? '',
            ],
            'hypotension' => [
                'student' => $checkboxes[20] ?? '',
                'father' => $checkboxes[21] ?? '',
                'mother' => $checkboxes[22] ?? '',
                'sibling' => $checkboxes[23] ?? '',
                'other' => $checkboxes[24] ?? '',
            ],
            'hyperthyroidism' => [
                'student' => $checkboxes[25] ?? '',
                'father' => $checkboxes[26] ?? '',
                'mother' => $checkboxes[27] ?? '',
                'sibling' => $checkboxes[28] ?? '',
                'other' => $checkboxes[29] ?? '',
            ],
            'hypothyroidism' => [
                'student' => $checkboxes[30] ?? '',
                'father' => $checkboxes[31] ?? '',
                'mother' => $checkboxes[32] ?? '',
                'sibling' => $checkboxes[33] ?? '',
                'other' => $checkboxes[34] ?? '',
            ],
            'anemia' => [
                'student' => $checkboxes[35] ?? '',
                'father' => $checkboxes[36] ?? '',
                'mother' => $checkboxes[37] ?? '',
                'sibling' => $checkboxes[38] ?? '',
                'other' => $checkboxes[39] ?? '',
            ],
            'other' => [
                'student' => $checkboxes[40] ?? '',
                'father' => $checkboxes[41] ?? '',
                'mother' => $checkboxes[42] ?? '',
                'sibling' => $checkboxes[43] ?? '',
                'other' => $checkboxes[44] ?? '',
            ],
        ];
    }

    /**
     * Build the family history concatenated string from checkbox form data.
     * Expects Checkbox1 through Checkbox45 in the data array.
     * 
     * @param array $data Form data containing Checkbox1-Checkbox45
     * @return string Concatenated string with dashes
     */
    public static function buildFamilyHistoryString(array $data): string
    {
        $checkboxes = [];
        
        for ($i = 1; $i <= 45; $i++) {
            $checkboxes[] = $data["Checkbox{$i}"] ?? '';
        }
        
        return implode('-', $checkboxes);
    }

    /**
     * Get a human-readable list of all vaccinations received.
     * 
     * @return array List of vaccine names that are marked as "Si"
     */
    public function getVaccinationsReceived(): array
    {
        $vaccines = [
            'va_dia' => __('Vacunas al día'),
            'vac1' => 'DTP/aP/DT',
            'vac2' => 'Polio',
            'vac3' => 'MMR',
            'vac4' => 'HIB',
            'vac5' => 'PPD',
            'vac6' => 'Varicella',
            'vac7' => 'HPV',
            'vac8' => 'HepA',
            'vac9' => 'HepB',
            'vac10' => 'Influenza',
            'vac11' => 'Measles',
            'vac12' => 'Meningo',
            'vac13' => 'Meningo B',
            'vac14' => 'Mumps',
            'vac15' => 'Pneumococcal',
            'vac16' => 'Rota',
            'vac17' => 'Rubella',
            'vac18' => 'Td/Tdap',
        ];

        $received = [];
        
        foreach ($vaccines as $field => $name) {
            if ($this->{$field} === 'Si') {
                $received[] = $name;
            }
        }

        return $received;
    }
}
