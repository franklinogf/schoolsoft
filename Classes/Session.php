<?php

namespace Classes;

use Classes\Route;

class Session
{
    public static function get($name)
    {
        return $_SESSION[$name];
    }

    public static function is_logged($reditect = true)
    {
        if (!isset($_SESSION['logged'])) {
            if ($reditect) {
                Route::redirect(__SUB_ROOT_URL);
            }
            return false;
        }        

        return true;
    }

    public static function id()
    {
        if (self::is_logged(false)) {
            return $_SESSION['logged']['user']['id'];
        }else{
            Route::redirect(__SUB_ROOT_URL);
        }
    }

    public static function type()
    {
        if (self::is_logged(false)) {
            return $_SESSION['logged']['type'];
        }else{
            Route::redirect(__SUB_ROOT_URL);
        }
    }

    // public static function redirect()
    // {
    //     if (self::is_logged(false)) {
    //         Route::redirect('/foro/'.$_SESSION['logged']['type']);

    //     }

    // }



}
