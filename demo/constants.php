<?php

use Illuminate\Database\Capsule\Manager;

$admin = Manager::connection('central')->table('schools')->where('id', __SCHOOL_ACRONYM)->first();
if ($admin) {

    $enviroments = json_decode($admin->enviroments);
    $features = json_decode($admin->features);

    if ($enviroments) {
        foreach ($enviroments as $key => $enviroment) {
            define("__" . strtoupper($key) . "__", $enviroment->value);
            define("__" . strtoupper($key) . "_OTHER__", $enviroment->other);
        }
    }
    if ($features) {
        foreach ($features as $key => $feature) {
            define("__" . strtoupper($key), $feature);
        }
    }
}
