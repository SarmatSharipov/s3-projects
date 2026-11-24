<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\View;
use App\Repositories\DocumentRepository;

final class ReportController
{
    public function status(): void
    {
        $repo = new DocumentRepository();
        $rows = $repo->statusReport();
        $totals = [
            'total' => 0, 'registered' => 0, 'review' => 0, 'in_progress' => 0,
            'approved' => 0, 'rejected' => 0, 'done' => 0,
        ];
        foreach ($rows as $r) {
            $totals['total'] += (int) $r['total'];
            $totals['registered'] += (int) $r['registered'];
            $totals['review'] += (int) $r['review'];
            $totals['in_progress'] += (int) $r['in_progress'];
            $totals['approved'] += (int) $r['approved'];
            $totals['rejected'] += (int) $r['rejected'];
            $totals['done'] += (int) $r['done'];
        }
        $view = new View(dirname(__DIR__, 2) . '/views');
        echo $view->render('reports/status', [
            'title'  => 'Ҳисоботи ҳуҷҷатҳо аз рӯи намуд',
            'rows'   => $rows,
            'totals' => $totals,
        ]);
    }
}
