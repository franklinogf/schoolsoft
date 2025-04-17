<?php

namespace Core;

use Illuminate\Database\Capsule\Manager as Capsule;


class Database
{

    public function __construct()
    {
        $schoolConfig = school_config('database', null);
        $appConfig = config('database');
        $capsule = new Capsule;
        if ($schoolConfig  !== null) {
            $capsule->addConnection([
                'driver' => 'mysql',
                'host' => $schoolConfig['host'] ?? '127.0.0.1',
                'database' => $schoolConfig['database'] ?? 'schoolsoft',
                'username' => $schoolConfig['username'] ?? 'root',
                'password' => $schoolConfig['password'] ?? '',
                'charset' => $schoolConfig['charset'] ?? 'utf8',
                'collation' => $schoolConfig['collation'] ?? 'utf8_unicode_ci',
                'port' => $schoolConfig['port'] ?? '3306',
                'prefix' => $schoolConfig['prefix'] ?? '',
            ], 'tenant');
        }

        $capsule->addConnection([
            'driver' => 'mysql',
            'host' => $appConfig['host'] ?? '127.0.0.1',
            'database' => $appConfig['database'] ?? 'schoolsoft',
            'username' => $appConfig['username'] ?? 'root',
            'password' => $appConfig['password'] ?? '',
            'charset' => $appConfig['charset'] ?? 'utf8',
            'collation' => $appConfig['collation'] ?? 'utf8_unicode_ci',
            'port' => $appConfig['port'] ?? '3306',
            'prefix' => $appConfig['prefix'] ?? '',
        ], 'central');


        $capsule->setAsGlobal();

        $capsule->bootEloquent();

        $capsule->getDatabaseManager()->setDefaultConnection('tenant');
    }
}
