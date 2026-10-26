<?php

namespace App\Services;

use Illuminate\Container\Container;
use Illuminate\Events\Dispatcher;
use Illuminate\Filesystem\Filesystem;
use Illuminate\View\Compilers\BladeCompiler;
use Illuminate\View\Engines\CompilerEngine;
use Illuminate\View\Engines\EngineResolver;
use Illuminate\View\Engines\PhpEngine;
use Illuminate\View\Factory;
use Illuminate\View\FileViewFinder;

/**
 * Standalone Blade renderer (illuminate/view without the full framework).
 * Templates live in `app/Views` (dot notation: `emails.autopay-reminder`)
 * and are compiled into `storage/views`.
 */
class ViewRenderer
{
    private static ?Factory $factory = null;

    /**
     * @param array<string, mixed> $data
     */
    public static function render(string $view, array $data = []): string
    {
        return self::factory()->make($view, $data)->render();
    }

    private static function factory(): Factory
    {
        if (self::$factory) {
            return self::$factory;
        }

        $files = new Filesystem();
        $cache = __DIR__ . '/../../storage/views';

        if (! $files->isDirectory($cache)) {
            $files->makeDirectory($cache, 0755, true);
        }

        $compiler = new BladeCompiler($files, $cache);
        $resolver = new EngineResolver();
        $resolver->register('blade', fn() => new CompilerEngine($compiler, $files));
        $resolver->register('php', fn() => new PhpEngine($files));

        return self::$factory = new Factory(
            $resolver,
            new FileViewFinder($files, [__DIR__ . '/../Views']),
            new Dispatcher(new Container()),
        );
    }
}
