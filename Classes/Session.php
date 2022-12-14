<?php

namespace Classes;

use Classes\Route;

class Session
{
    // 4 hours in seconds (4*60*60)
    private static $sessionDuration = 14400;

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
        $location = str_replace('/', "", __SUB_ROOT_URL);
        $logged = false;
        if (isset($_SESSION['logged'])) {
            if (time() - $_SESSION['start'] > self::$sessionDuration) {
                session_unset();
                session_destroy();
                $logged = false;
            } else {
                if ($_SESSION['logged']['location'] !== $location) {
                    $logged = false;
                    session_unset();
                    session_destroy();
                } else {
                    $logged = true;
                }
            }
        } else {
            $logged = false;
        }

        if (!$logged) {
            if ($redirect) {
                Route::redirect();
            }
        }
        return $logged;
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
    public static function location()
    {
        if (self::is_logged(false)) {
            return $_SESSION['logged']['location'];
        }
        return false;
    }
}
