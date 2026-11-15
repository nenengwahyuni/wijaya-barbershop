<?php

namespace App\Core;

class View
{
    public static function render(string $view, array $data = [], ?string $layout = 'main'): string
    {
        $baseViewsDir = dirname(__DIR__, 2) . '/resources/views/';
        $viewFile = $baseViewsDir . str_replace('.', '/', $view) . '.php';

        if (!file_exists($viewFile)) {
            return "View [{$view}] tidak ditemukan di {$viewFile}";
        }

        // Ekstrak data variabel ke view scope
        extract($data);

        // Render konten view
        ob_start();
        include $viewFile;
        $content = ob_get_clean();

        // Jika tidak memakai layout, langsung return konten
        if ($layout === null) {
            echo $content;
            return $content;
        }

        $layoutFile = $baseViewsDir . 'layouts/' . $layout . '.php';
        if (file_exists($layoutFile)) {
            ob_start();
            include $layoutFile;
            $fullHtml = ob_get_clean();
            echo $fullHtml;
            return $fullHtml;
        }

        echo $content;
        return $content;
    }
}

