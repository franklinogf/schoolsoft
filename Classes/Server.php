<?php

namespace Classes;

use Classes\Route;

class Server
{
    public static function get($name)
    {
        return $_SERVER[$name];
    }

    public static function is_post($errorPage = true)
    {
        if (self::get('REQUEST_METHOD') !== "POST") {
            if ($errorPage) {
                Route::error();
            }
            return false;
        }
        return true;
    }
}
