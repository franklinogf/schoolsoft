<?php

namespace Classes;

use Classes\Route;

class Session
{
    public static function set($name, $value)
    {
        $_SESSION[$name] = $value;
    }

    public static function get($name, $delete = false)
    {
        if (isset($_SESSION[$name])) {
            $sessionValue = $_SESSION[$name];
            if ($delete) {
                self::delete($name);
            }
            return $sessionValue;
        }
        return false;
    }

    public static function delete($name)
    {
        unset($_SESSION[$name]);
    }

    public static function is_logged($redirect = true)
    {
        if (!isset($_SESSION['logged'])) {
            if ($redirect) {
                Route::redirect();
            }
            return false;
        }

        return true;
    }

    public static function id()
    {
        if (self::is_logged(false)) {
            return $_SESSION['logged']['user']['id'];
        }
        return false;
    }

    public static function type()
    {
        if (self::is_logged(false)) {
            return $_SESSION['logged']['type'];
        }
        return false;
    }

    // public static function redirect()
    // {
    //     if (self::is_logged(false)) {
    //         Route::redirect('/'.$_SESSION['logged']['type']);

    //     }

    // }



}
