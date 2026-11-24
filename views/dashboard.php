<?php
/** @var App\Core\View $view */
$statusLabels = [
    'registered' => 'Бақайдгирифта',
    'review' => 'Дар баррасӣ',
    'in_progress' => 'Дар иҷро',
    'approved' => 'Тасдиқшуда',
    'rejected' => 'Радшуда',
    'done' => 'Иҷрошуда',
];
ob_start();
?>
<div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-4">
  <h1 class="h3 m-0">Саҳифаи асосӣ</h1>
  <a class="btn btn-primary" href="/documents/create">+ Ҳуҷҷати нав</a>
</div>
<div class="row g-3 mb-4">
  <div class="col-6 col-md-3">
    <div class="panel text-center">
      <div class="display-6"><?= (int)$total ?></div>
      <div class="text-muted-2">Ҳамаи ҳуҷҷатҳо</div>
    </div>
  </div>
  <?php foreach (['review', 'in_progress', 'approved'] as $st): ?>
  <div class="col-6 col-md-3">
    <div class="panel text-center">
      <div class="display-6"><?= (int)($stats[$st] ?? 0) ?></div>
      <div class="text-muted-2"><?= $statusLabels[$st] ?></div>
    </div>
  </div>
  <?php endforeach; ?>
</div>
<h2 class="h5 mb-3">Ҳуҷҷатҳои охирин</h2>
<div class="panel p-0">
  <div class="table-responsive">
    <table class="table table-hover table-docs mb-0">
      <thead>
        <tr>
          <th>Рақам</th>
          <th>Мавзӯъ</th>
          <th>Ҳолат</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($recent as $d): ?>
        <tr>
          <td><a href="/documents/<?= (int)$d['id'] ?>"><?= $view->e($d['number']) ?></a></td>
          <td><?= $view->e($d['subject']) ?></td>
          <td><span class="badge-status st-<?= $view->e($d['status']) ?>"><?= $statusLabels[$d['status']] ?? $d['status'] ?></span></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
<?php
$content = ob_get_clean();
require __DIR__ . '/layout.php';
