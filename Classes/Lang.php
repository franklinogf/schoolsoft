<?php

namespace Classes;

class Lang
{

    private static $translation = [];
    private static $menuTranslation = [];
    public static $select = (__LANG === 'es') ? 'Seleccionar' : 'Select';
    public static $continue = (__LANG === 'es') ? 'Continuar' : 'Continue';
    public static $grade = (__LANG === 'es') ? 'Curso' : 'Grade';
    public static $score = (__LANG === 'es') ? 'Nota' : 'Grade';
    public static $trimester = (__LANG === 'es') ? 'Trimestre' : 'Trimester';
    public static $page = (__LANG === 'es') ? 'Pagina' : 'Page';

    public static function addTranslation($translationArray)
    {
        self::$translation = $translationArray[__LANG];
    }

    public static function addMenutranslation($translationArray)
    {
        self::$menuTranslation = $translationArray[__LANG];
    }

    public static function translation($text)
    {
        if (self::$translation !== null) {
           if(isset(self::$translation[$text])){
               return self::$translation[$text];
           }else{
            return "No hay traducción para {$text}";
           }
        }
        throw new \Exception("Primero se debe de agregar un array con las traducciones");
    }

    public static function menuTranslation($text)
    {
        if (self::$menuTranslation !== null) {
           if(isset(self::$menuTranslation[$text])){
               return self::$menuTranslation[$text];
           }else{
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
        return $text.'s';
    }


    
}
