<?php

namespace Core;

use Illuminate\Translation\FileLoader;
use Illuminate\Filesystem\Filesystem;

class JsonAwareFileLoader extends FileLoader
{
    protected $basePath;

    public function __construct(Filesystem $files, $path)
    {
        parent::__construct($files, $path);
        $this->basePath = $path;
    }

    public function load($locale, $group, $namespace = null)
    {
        $lines = parent::load($locale, $group, $namespace);

        if ($group === '*') {
            $path = $this->basePath . '/' . $locale . '.json';
            if ($this->files->exists($path)) {
                $json = json_decode($this->files->get($path), true);
                if (is_array($json)) {
                    $lines = array_merge($lines, $json);
                }
            }
        }

        return $lines;
    }
}
