<?php

namespace Classes;

class Lang
{

    private $translations = [];

    public function __construct(array $translation = [])
    {
        if (count($translation) > 0) {
            $this->addTranslation($translation);
        }
        $defaultTranslations = [
            ['Seleccionar', 'Select'],
            ['Continuar', 'Continue'],
            ['Curso', 'Grade'],
            ['Nota', 'Grade'],
            ['Trimestre', 'Trimester'],
            ['Pagina', 'Page'],
            ['Derechos reservados', 'Copy Right'],
        ];
        $this->addTranslation($defaultTranslations);
    }
    // can be used after initialization
    public function addTranslation(array $translationArray)
    {
        foreach ($translationArray as $translation) {
            $this->translations[strtolower($translation[0])]['es'] = $translation[0];
            $this->translations[strtolower($translation[0])]['en'] = $translation[1];
        }
    }

    public function translation($text)
    {
        return $this->translations[strtolower($text)][__LANG];
    }

    // public static function translation($text)
    // {
    //     if (self::$translation !== null) {
    //         if (isset(self::$translation[$text])) {
    //             return self::$translation[$text];
    //         } else {
    //             return "No hay traducción para {$text}";
    //         }
    //     }
    //     throw new \Exception("Primero se debe de agregar un array con las traducciones");
    // }

    // public static function menuTranslation($text)
    // {
    //     if (self::$menuTranslation !== null) {
    //         if (isset(self::$menuTranslation[$text])) {
    //             return self::$menuTranslation[$text];
    //         } else {
    //             return "No hay traducción para {$text}";
    //         }
    //     }
    //     throw new \Exception("Primero se debe de agregar un array con las traducciones del menu");
    // }

    // public static function translate($spanishText, $englishText)
    // {
    //     return (__LANG === 'es') ? $spanishText : $englishText;
    // }

    // public static function plural($text)
    // {
    //     return $text . 's';
    // }
}
