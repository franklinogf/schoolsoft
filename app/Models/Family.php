<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Family extends Model
{
    protected $table = 'madre';
    protected $primaryKey = 'id';
    public $timestamps = false;
    protected $guarded = [];
}
