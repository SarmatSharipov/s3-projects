<?php
declare(strict_types=1);

namespace App\Core;

final class Router
{
    private array $routes = [];

    public function get(string $path, array $handler): void
    {
        $this->routes[] = ['GET', $path, $handler];
    }

    public function post(string $path, array $handler): void
    {
        $this->routes[] = ['POST', $path, $handler];
    }

    public function dispatch(string $method, string $uri): void
    {
        $path = parse_url($uri, PHP_URL_PATH) ?: '/';
        $path = rtrim($path, '/') ?: '/';

        foreach ($this->routes as [$routeMethod, $routePath, $handler]) {
            if ($routeMethod !== $method) {
                continue;
            }
            $pattern = preg_replace('#\{([a-zA-Z_]+)\}#', '(?P<$1>[^/]+)', $routePath);
            $pattern = '#^' . $pattern . '$#';
            if (!preg_match($pattern, $path, $matches)) {
                continue;
            }
            $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
            [$class, $action] = $handler;
            $controller = new $class();
            $args = [];
            foreach ($params as $value) {
                $args[] = is_numeric($value) ? (int) $value : $value;
            }
            $controller->$action(...$args);
            return;
        }
        http_response_code(404);
        echo (new View(dirname(__DIR__, 2) . '/views'))->render('errors/error', [
            'title' => 'Саҳифа ёфт нашуд',
            'code' => 404,
            'message' => 'Саҳифаи дархостшуда вуҷуд надорад.',
        ]);
    }
}
