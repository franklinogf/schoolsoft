<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class School extends Model
{
    protected $connection = 'central';

    public static function current(): School
    {
        $school = (new self)->where('id', __SCHOOL_ACRONYM)->first();
        if (!$school) {
            throw new \Exception("School not found with acronym: " . __SCHOOL_ACRONYM);
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
