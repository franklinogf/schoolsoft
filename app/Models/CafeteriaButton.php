<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CafeteriaButton extends Model
{
    protected $table = 'T_cafeteria';
    protected $primaryKey = 'id';
    protected $guarded = [];

    public $timestamps = false;
}
