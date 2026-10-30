<?php

declare(strict_types=1);

namespace App\Core;

use RuntimeException;

final class View
{
    /**
     * @param array<string, mixed> $data
     */
    public static function render(string $template, array $data = []): Response
    {
        $path = BASE_PATH . '/app/Views/' . ltrim($template, '/') . '.php';
        if (!is_file($path)) {
            throw new RuntimeException('View template not found.');
        }

        extract($data, EXTR_SKIP);
        ob_start();
        require $path;
        $content = ob_get_clean();

        if (!is_string($content)) {
            throw new RuntimeException('Unable to render view.');
        }

        return Response::html($content);
    }
}
