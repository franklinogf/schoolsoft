<?php

use App\Enums\LanguageCode;
use Core\TranslatorFactory;
use Dotenv\Dotenv;
use Illuminate\Database\Eloquent\Relations\Relation;

require __DIR__ . "/vendor/autoload.php";
require __DIR__ . '/core/translator.php';


$dotenv = Dotenv::createImmutable(__DIR__); // path to your .env file
$dotenv->load();

TranslatorFactory::get()->setLocale(config('app.locale', LanguageCode::SPANISH->value)); // default locale

Relation::enforceMorphMap([
    'student' => \App\Models\Student::class,
    'admin' => \App\Models\Admin::class,
]);

define('__ROOT', str_replace('/', DIRECTORY_SEPARATOR, dirname(__DIR__))); # /home/admin/public_html
