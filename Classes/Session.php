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

        if (!isset($_SESSION['logged'])) {
            if ($redirect) {
                Route::redirect();
            }
            return false;
        }

        $location = str_replace('/', "", __SUB_ROOT_URL);

        $logged = true;
        if ($_SESSION['logged']['acronym'] !== __SCHOOL_ACRONYM) {
            $logged = false;
        } else if (time() - $_SESSION['start'] > self::$sessionDuration) {
            $logged = false;
        } else if ($_SESSION['logged']['location'] !== $location) {
            $logged = false;
        }

        if (!$logged) {
            session_unset();
            session_destroy();
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
            return isset($_SESSION['logged']) ? $_SESSION['logged']['user']['id'] : false;
        }
        return false;
    }

    public static function type()
    {
        if (self::is_logged(false)) {
            return isset($_SESSION['logged']) ? $_SESSION['logged']['type'] : false;
        }
        return false;
    }
    public static function location()
    {
        if (self::is_logged(false)) {
            return isset($_SESSION['logged']) ? $_SESSION['logged']['location'] : false;
        }
        return false;
    }
}
