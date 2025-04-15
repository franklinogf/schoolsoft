<?php

namespace Core;

use Illuminate\Database\Capsule\Manager as Capsule;


class Database
{

    public function __construct()
    {
        $config = school_config('database');
        $capsule = new Capsule;
        $capsule->addConnection([
            'driver' => 'mysql',
            'host' => $config['host'] ?? '127.0.0.1',
            'database' => $config['database'] ?? 'schoolsoft',
            'username' => $config['username'] ?? 'root',
            'password' => $config['password'] ?? '',
            'charset' => $config['charset'] ?? 'utf8',
            'collation' => $config['collation'] ?? 'utf8_unicode_ci',
            'port' => $config['port'] ?? '3306',
            'prefix' => $config['prefix'] ?? '',
        ], 'mysql');


        $capsule->setAsGlobal();

        $capsule->bootEloquent();

        $capsule->getDatabaseManager()->setDefaultConnection('mysql');
    }
}
