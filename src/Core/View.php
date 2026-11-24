<?php
declare(strict_types=1);

namespace App\Core;

final class View
{
    public function __construct(private readonly string $basePath)
    {
    }

    public function render(string $template, array $data = []): string
    {
        extract($data, EXTR_SKIP);
        $view = $this;
        ob_start();
        require $this->basePath . '/' . $template . '.php';
        return (string) ob_get_clean();
    }

    public function e(?string $value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    public function url(string $path): string
    {
        return $path;
    }
}
