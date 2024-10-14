<?php
require_once 'app.php';
use Classes\DataBase\DB;
$admin = DB::admin()->first();
$enviroments = json_decode($admin->enviroments);
$features = json_decode($admin->features);

foreach ($enviroments as $key => $enviroment) {
    define("__" . strtoupper($key) . "__", $enviroment->value);
    define("__" . strtoupper($key) . "_OTHER__", $enviroment->other);
}
foreach ($features as $key => $feature) {
    define("__" . strtoupper($key) . "__", $feature);
}
echo __WHATSAPP__;



