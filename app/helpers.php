<?php

use Core\TranslatorFactory;

if (!function_exists('dd')) {
    function dd($data): never
    {
        echo "<pre>";
        var_dump($data);
        echo "</pre>";
        die;
    }
}

if (!function_exists('config')) {
    function config(string $keys): mixed
    {

        $keys = explode('.', $keys);

        if (is_string($keys)) {
            $config = require __DIR__ . "/../config/{$keys}.php";
        } else {
            $config = require __DIR__ . "/../config/{$keys[0]}.php";
        }

        if (count($keys) < 2) {
            return $config;
        }

        if (count($keys) > 3) {
            return $config[$keys[1]][$keys[2]][$keys[3]] ?? null;
        }

        if (count($keys) > 2) {
            return $config[$keys[1]][$keys[2]] ?? null;
        }
        return $config[$keys[1]] ?? null;
    }
}

if (!function_exists('school_config')) {
    function school_config(string $keys): mixed
    {
        $keys = explode('.', $keys);

        $config = require  __ROOT_SCHOOL . "/config/{$keys[0]}.php";
        if (count($keys) < 2) {
            return $config;
        }

        if (count($keys) > 3) {
            return $config[$keys[1]][$keys[2]][$keys[3]] ?? null;
        }

        if (count($keys) > 2) {
            return $config[$keys[1]][$keys[2]] ?? null;
        }
        return $config[$keys[1]] ?? null;
    }
}



if (!function_exists('school_asset')) {
    function school_asset(string $path): string
    {
        $appUrl = config('app.url');

        $acronym = school_config('app.acronym');

        return  "$appUrl/$acronym/$path";
    }
}

if (!function_exists('school_logo')) {
    function school_logo(): string
    {
        return  school_asset(school_config('app.logo.default'));
    }
}
