<?php

namespace Classes\Services;

use ScssPhp\ScssPhp\Compiler;
use ScssPhp\ScssPhp\OutputStyle;

class SccsCompiler
{

    public function compile(
        array $theme,
    ): bool {
        if (empty($theme) || !is_array($theme) || !isset($theme['colors']) || !isset($theme['booleans'])) {
            return false;
        }

        $defaultTheme = include __ROOT . '/config/theme.php';

        $colors = $defaultTheme['colors'];
        $booleans = $defaultTheme['booleans'];
        $themeColors = "";

        $themeBooleans = "";
        foreach ($colors as $key => $value) {
            $value = isset($theme['colors'][$key]) ? $theme['colors'][$key] : $value;
            $themeColors .= "{$key}: {$value},";
        }

        foreach ($booleans as $key => $value) {
            $value = isset($theme['booleans'][$key]) ? ((bool) $theme['booleans'][$key] ? 'true' : 'false') : ((bool) $value ? 'true' : 'false');
            $themeBooleans .=  "\${$key}: {$value};";
        }

        try {
            $compiler = new Compiler();
            $compiler->setOutputStyle(OutputStyle::COMPRESSED);
            $compiler->setImportPaths(__ROOT . '/node_modules/bootstrap/scss/');

            $result = $compiler->compileString("
                    \$theme-colors: (
                        $themeColors
                    );

                    $themeBooleans
                    
                    \$enable-shadows: true;  
                    
                    \$enable-responsive-font-sizes: true;  
                    
                    @import 'bootstrap.scss';
            ");

            $css = $result->getCss();
            $this->createSchoolCssDirectory();

            file_put_contents(__ROOT_SCHOOL . '/css/main-bootstrap.css', $css);
            return true;
        } catch (\Throwable) {
            return false;
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
