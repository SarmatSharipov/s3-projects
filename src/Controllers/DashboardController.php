<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\View;
use App\Repositories\DocumentRepository;

final class DashboardController
{
    public function index(): void
    {
        $repo = new DocumentRepository();
        $stats = $repo->countByStatus();
        $recent = $repo->recent(6);
        $view = new View(dirname(__DIR__, 2) . '/views');
        echo $view->render('dashboard', [
            'title'  => 'Саҳифаи асосӣ',
            'stats'  => $stats,
            'recent' => $recent,
            'total'  => array_sum($stats),
        ]);
    }
}
