<?php

namespace Classes;

class Lang
{

    private $translations = [];

    public function __construct(array $translation = [])
    {
        $defaultTranslations = [
            ['Seleccionar', 'Select'],
            ['Continuar', 'Continue'],
            ['Curso', 'Grade'],
            ['Nota', 'Grade'],
            ['Trimestre', 'Trimester'],
            ['Pagina', 'Page'],
            ['Derechos reservados', 'Copy Right'],
            ["Notas", __SCHOOL_ACRONYM !== 'cbtm' ? "Grades" : "Grades/Test"],
            ["Notas de verano", "Summer grades"],
            ["Pruebas cortas", "Quiz"],
            ["Trabajos diarios", __SCHOOL_ACRONYM !== 'cbtm' ? "Daily homework" : "Participation"],
            ["Trabajos de libreta", "Homework"],
            ["Conducta y asistencia", "Behavior and attendance"],
            ["Examen final", "Final exam"],
        ];
        $this->addTranslation($defaultTranslations);
        if (count($translation) > 0) {
            $this->addTranslation($translation);
        }
    }
    // can be used after initialization
    public function addTranslation(array $translationArray)
    {
        foreach ($translationArray as $translation) {
            $this->translations[strtolower(utf8_decode($translation[0]))]['es'] = $translation[0];
            $this->translations[strtolower(utf8_decode($translation[0]))]['en'] = $translation[1];
        }
    }

    public function translation($text)
    {
        $key = strtolower(utf8_decode($text));
        if (!isset($this->translations[$key])) {
            return $text;
        }
        return $this->translations[$key][__LANG];
    }

    public function trimesterTranslation($text)
    {
        return str_replace('Trimestre', $this->translation("Trimestre"), str_replace('-', ' ', $text));
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
