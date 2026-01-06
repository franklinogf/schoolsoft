<?php

use Core\TranslatorFactory;
use Illuminate\Support\Str;

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
    function config(string $keys, mixed $default = null): mixed
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
            return $config[$keys[1]][$keys[2]][$keys[3]] ?? $default;
        }

        if (count($keys) > 2) {
            return $config[$keys[1]][$keys[2]] ?? $default;
        }
        return $config[$keys[1]] ?? $default;
    }
}

if (!function_exists('school_config')) {
    function school_config(string $keys, mixed $default = null): mixed
    {
        $keys = explode('.', $keys);


        if (is_string($keys)) {
            $config = require __ROOT_SCHOOL . "/config/{$keys}.php";
        } else {
            $config = require __ROOT_SCHOOL . "/config/{$keys[0]}.php";
        }

        if (count($keys) < 2) {
            return $config;
        }

        if (count($keys) > 3) {
            return $config[$keys[1]][$keys[2]][$keys[3]] ?? $default;
        }

        if (count($keys) > 2) {
            return $config[$keys[1]][$keys[2]] ?? $default;
        }
        return $config[$keys[1]] ?? $default;
    }
}
if( !function_exists('school_is')){
    function school_is(string ...$acronym): bool
    {
        return in_array(school_config('app.acronym'), $acronym);
    }
}
if (!function_exists('asset')) {
    function asset(string $path): string
    {
        $appUrl = config('app.url');
        return "$appUrl/$path";
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

if (!function_exists('school_asset_path')) {
    function school_asset_path(string $path): string
    {

        $acronym = school_config('app.acronym');

        return  __ROOT . "/$acronym/$path";
    }
}

if (!function_exists('school_logo')) {
    function school_logo(): string
    {
        return  school_asset(school_config('app.logo.default'));
    }
}
if (!function_exists('school_logo_path')) {
    function school_logo_path(): string
    {
        return  school_asset_path(school_config('app.logo.default'));
    }
}

if (!function_exists('__')) {
    function __(string $key, array $replace = [], ?string $locale = null): string
    {
        return TranslatorFactory::get()->get($key, $replace, $locale);
    }
}

if (!function_exists('trans_choice')) {
    function trans_choice(string $key, int $choice, array $replace = [], ?string $locale = null): string
    {
        return TranslatorFactory::get()->choice($key, $choice, $replace, $locale);
    }
}

if (!function_exists('attachments_path')) {
    function attachments_path(?string $path = null): string
    {
        $path = $path ?  ltrim($path, '/') : '';
        return __DIR__ . '/../attachments/' . $path;
    }
}

if (!function_exists('attachments_url')) {
    function attachments_url(?string $path = null): string
    {
        $path = $path ?  ltrim($path, '/') : '';
        return asset("attachments/$path");
    }
}

if (!function_exists("upload_attachment")) {
    function upload_attachment(array $files, string $path = ''): array
    {
        if (!is_dir(attachments_path($path))) {
            mkdir(attachments_path($path), 0777, true);
        }
        $urls = [];


        foreach ($files['tmp_name'] as $index => $file) {
            $fileName = Str::uuid()->toString();
            $fileExtension = pathinfo($files['name'][$index], PATHINFO_EXTENSION);
            $filePath = attachments_path("$path/$fileName.$fileExtension");
            if (move_uploaded_file($file, $filePath)) {
                $urls[] = attachments_url("$path/$fileName.$fileExtension");
            }
        }

        return $urls;
    }
}

if (!function_exists('school_has_active')) {
    function school_has_active(string $schoolConfig): bool
    {
        return in_array(school_config('app.acronym'), config($schoolConfig, []));
    }
}
