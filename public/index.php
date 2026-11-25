<?php
declare(strict_types=1);

define('BASE_PATH', dirname(__DIR__));

if (is_file(BASE_PATH . '/vendor/autoload.php')) {
    require BASE_PATH . '/vendor/autoload.php';
} else {
    spl_autoload_register(function (string $class): void {
        if (str_starts_with($class, 'App\\')) {
            $file = BASE_PATH . '/src/' . str_replace('\\', '/', substr($class, 4)) . '.php';
            if (is_file($file)) {
                require $file;
            }
        }
    });
}

$config = require BASE_PATH . '/config/config.php';
App\Core\Database::configure($config['db']);

set_exception_handler(function (Throwable $e) use ($config): void {
    error_log((string) $e);
    http_response_code(500);
    echo (new App\Core\View(BASE_PATH . '/views'))->render('errors/error', [
        'title'   => 'Хатои дохилии сервер',
        'code'    => 500,
        'message' => $config['debug'] ? $e->getMessage() : 'Баъдтар кӯшиш кунед.',
    ]);
});

$router = new App\Core\Router();
$router->get('/', [App\Controllers\DashboardController::class, 'index']);
$router->get('/documents', [App\Controllers\DocumentController::class, 'index']);
$router->get('/documents/create', [App\Controllers\DocumentController::class, 'create']);
$router->post('/documents/create', [App\Controllers\DocumentController::class, 'store']);
$router->get('/documents/{id}', [App\Controllers\DocumentController::class, 'show']);
$router->get('/documents/{id}/edit', [App\Controllers\DocumentController::class, 'edit']);
$router->post('/documents/{id}/edit', [App\Controllers\DocumentController::class, 'update']);
$router->post('/documents/{id}/delete', [App\Controllers\DocumentController::class, 'delete']);
$router->get('/reports/status', [App\Controllers\ReportController::class, 'status']);

$router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI'] ?? '/');
