<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
/**
 * @property int $id
 * @property string $name
 * @property string $guard_name
 * @property string $created_at
 * @property string $updated_at
 */
class Permission extends Model
{
    protected $table = 'permissions';

    protected $guarded = [];
}
