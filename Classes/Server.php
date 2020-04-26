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

    public static function referer($value){
        
        $file = basename(self::get('HTTP_REFERER'));
        $pathFile = str_replace('.php', '', $file);
        if($pathFile === $value){
            return true;
        }
        return false;
    }
}
