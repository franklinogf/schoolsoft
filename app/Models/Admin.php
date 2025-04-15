<?php

namespace App\Models;

use Classes\Session;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;


class Admin extends Model
{
    protected $table = 'colegio';
    protected $primaryKey = 'usuario';
    protected $keyType = 'string';
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

    public function casts()
    {
        return [
            'environments' => 'array',
            'constants' => 'array',
            'theme' => 'json'
        ];
    }
}
