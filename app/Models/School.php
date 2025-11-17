<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
/**
 * @property int $id
 * @property array $environments
 * @property array $features
 * @property array $theme
 * @property array $pdf
 * @property array $data
 */
class School extends Model
{
    protected $connection = 'central';

    public static function current(): School
    {
        $acronym = school_config('app.acronym');

        if (!$acronym) {
            throw new \Exception("School acronym not set in configuration.");
        }

        $school = (new self)->where('id', $acronym)->first();

        if (!$school) {
            throw new \Exception("School not found with acronym: " . $acronym);
        }

        return $school;
    }

    protected function casts(): array
    {
        return [
            'environments' => 'array',
            'features' => 'array',
            'theme' => 'array',
            'pdf' => 'array',
            'data' => 'array',
        ];
    }
}
