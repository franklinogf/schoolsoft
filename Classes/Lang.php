<?php

namespace Classes;

class Lang
{

    private static $translation = [];
    private static $menuTranslation = [];
    public static $trans = [];
   

    private static function init()
    {
        self::$trans['select'] = (__LANG === 'es') ? 'Seleccionar' : 'Select';
        self::$trans['continue'] = (__LANG === 'es') ? 'Continuar' : 'Continue';
        self::$trans['grade'] = (__LANG === 'es') ? 'Curso' : 'Grade';
        self::$trans['score'] = (__LANG === 'es') ? 'Nota' : 'Grade';
        self::$trans['trimester'] = (__LANG === 'es') ? 'Trimestre' : 'Trimester';
        self::$trans['page'] = (__LANG === 'es') ? 'Pagina' : 'Page';
    }
    public static function addTranslation($translationArray)
    {
        self::init();
        self::$translation = $translationArray[__LANG];
    }

    public static function addMenutranslation($translationArray)
    {
        self::init();
        self::$menuTranslation = $translationArray[__LANG];
    }

    public static function translation($text)
    {
        if (self::$translation !== null) {
            if (isset(self::$translation[$text])) {
                return self::$translation[$text];
            } else {
                return "No hay traducción para {$text}";
            }
        }
        throw new \Exception("Primero se debe de agregar un array con las traducciones");
    }

    public static function menuTranslation($text)
    {
        if (self::$menuTranslation !== null) {
            if (isset(self::$menuTranslation[$text])) {
                return self::$menuTranslation[$text];
            } else {
                return "No hay traducción para {$text}";
            }
        }
        throw new \Exception("Primero se debe de agregar un array con las traducciones del menu");
    }

    public static function translate($spanishText, $englishText)
    {
        return (__LANG === 'es') ? $spanishText : $englishText;
    }

    public static function plural($text)
    {
        return $text . 's';
    }
}
