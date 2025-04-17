<?php

namespace Core;


use Illuminate\Translation\Translator;
use Illuminate\Filesystem\Filesystem;

class TranslatorFactory
{
    protected static $translator;

    public static function get()
    {
        if (!self::$translator) {
            $filesystem = new Filesystem();
            $loader = new JsonAwareFileLoader($filesystem, dirname(__DIR__) . '/lang');
            self::$translator = new Translator($loader, 'en');
        }

        return self::$translator;
    }
}
