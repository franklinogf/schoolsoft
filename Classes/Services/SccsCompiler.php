<?php

namespace Classes\Services;

use ScssPhp\ScssPhp\Compiler;
use ScssPhp\ScssPhp\OutputStyle;

class SccsCompiler
{

    public function compile(
        ?string $primary = null,
        ?string $secondary = null,
        ?string $light = null,
        ?bool $enable_shadows = null,
        ?bool $enable_responsive_font_sizes = null,
    ): void {

        $config = include __ROOT . '/config/theme.php';

        $primary ??= $config['theme-colors']['primary'];
        $secondary ??= $config['theme-colors']['secondary'];
        $light ??= $config['theme-colors']['light'];
        $enable_shadows ??= $config['enable-shadows'];
        $enable_responsive_font_sizes ??= $config['enable-responsive-font-sizes'];

        try {
            $compiler = new Compiler();
            $compiler->setOutputStyle(OutputStyle::COMPRESSED);
            $compiler->setImportPaths(__ROOT . '/node_modules/bootstrap/scss/');

            $result = $compiler->compileString('
                    $theme-colors: (
                        primary: ' . $primary . ',
                        secondary: ' . $secondary . ',
                        light: ' . $light . '
                    );
                    
                    $enable-shadows: ' . ($enable_shadows ? 'true' : 'false') . ';  
                    $enable-responsive-font-sizes: ' . ($enable_responsive_font_sizes ? 'true' : 'false') . ';  
                    
                    @import "bootstrap.scss";
            ');

            $css = $result->getCss();
            $this->createSchoolCssDirectory();

            file_put_contents(__ROOT_SCHOOL . '/css/main-bootstrap.css', $css);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function useDefault(): void
    {
        $this->removeSchoolCssDirectory();
    }

    private function removeSchoolCssDirectory(): void
    {
        if (is_dir(__ROOT_SCHOOL . '/css')) {
            array_map('unlink', glob(__ROOT_SCHOOL . '/css/*'));
            rmdir(__ROOT_SCHOOL . '/css');
        }
    }
    private function createSchoolCssDirectory(): void
    {
        if (!is_dir(__ROOT_SCHOOL . '/css')) {
            mkdir(__ROOT_SCHOOL . '/css', 0777, true);
        }
    }
}
