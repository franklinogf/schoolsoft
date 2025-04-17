<?php

namespace App\Models;


use Classes\Session;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HasPermissions;

class Admin extends Model
{
    use HasPermissions;
    protected $table = 'colegio';
    public $incrementing = false;
    public $timestamps = false;

    protected $guarded = [];



    public function info(string $key): mixed
    {
        return $this->{$key} ?? null;
    }

    public function year(): string
    {
        $yearToUse = Session::location() === 'admin' ? 'year2' : 'year';
        return $this->{$yearToUse};
    }


    protected function scopePrimaryAdmin(Builder $query): void
    {
        $query->where('usuario', 'administrador');
    }
    protected function scopeUser(Builder $query, string $user): void
    {
        $query->where('usuario', $user);
    }



    public function casts()
    {
        return [
            'environments' => 'array',
            'constants' => 'array',
            'theme' => 'json',
            'pdf' => 'json'
        ];
    }
}
