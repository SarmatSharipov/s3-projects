<?php
require 'config.php';
require_login();

$period = $_GET['period'] ?? 'month';
switch ($period) {
    case 'quarter': $q = ceil(date('n') / 3); $from = date('Y-m-d', mktime(0, 0, 0, ($q - 1) * 3 + 1, 1)); break;
    case 'year':    $from = date('Y-01-01'); break;
    default:        $period = 'month'; $from = date('Y-m-01');
}
$to = date('Y-m-d');
$label = ['month' => 'Моҳ', 'quarter' => 'Семоҳа', 'year' => 'Сол'][$period];

$byStatus = $pdo->prepare('SELECT status, COUNT(*) c FROM documents WHERE doc_date BETWEEN ? AND ? GROUP BY status');
$byStatus->execute([$from, $to]); $byStatus = $byStatus->fetchAll(PDO::FETCH_KEY_PAIR);

$byType = $pdo->prepare('SELECT type, COUNT(*) c FROM documents WHERE doc_date BETWEEN ? AND ? GROUP BY type');
$byType->execute([$from, $to]); $byType = $byType->fetchAll(PDO::FETCH_KEY_PAIR);

$tk = $pdo->prepare("SELECT
    SUM(status='done') done,
    SUM(status<>'done' AND deadline>=NOW()) pending,
    SUM(status<>'done' AND deadline<NOW()) overdue
  FROM tasks WHERE DATE(created_at) BETWEEN ? AND ?");
$tk->execute([$from, $to]); $tasks = $tk->fetch();

$rows = [['Нишондиҳанда', 'Миқдор']];
foreach (DOC_STATUS as $k => $v) $rows[] = ['Ҳуҷҷатҳо: ' . $v, $byStatus[$k] ?? 0];
foreach (DOC_TYPES as $k => $v) $rows[] = ['Намуд: ' . $v, $byType[$k] ?? 0];
$rows[] = ['Супоришҳои иҷрошуда', $tasks['done'] ?? 0];
$rows[] = ['Супоришҳои иҷронашуда (дар мӯҳлат)', $tasks['pending'] ?? 0];
$rows[] = ['Супоришҳои мӯҳлатгузашта', $tasks['overdue'] ?? 0];

if (isset($_GET['export'])) { // CSV — Excel мекушояд
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="report_' . $period . '_' . date('Ymd') . '.csv"');
    echo "\xEF\xBB\xBF";
    $out = fopen('php://output', 'w');
    fputcsv($out, ["Ҳисобот: $label", "$from — $to"], ';');
    foreach ($rows as $r) fputcsv($out, $r, ';');
    exit;
}
include 'includes/header.php';
?>
<div class="d-flex justify-content-between align-items-center mb-3 d-print-none">
  <h4>Ҳисоботҳо</h4>
  <div class="btn-group">
    <?php foreach (['month' => 'Моҳ', 'quarter' => 'Семоҳа', 'year' => 'Сол'] as $k => $v): ?>
      <a class="btn btn-outline-primary <?= $period === $k ? 'active' : '' ?>" href="?period=<?= $k ?>"><?= $v ?></a>
    <?php endforeach; ?>
  </div>
  <div>
    <a class="btn btn-success" href="?period=<?= $period ?>&export=1">Содирот ба Excel</a>
    <button class="btn btn-secondary" onclick="window.print()">Чоп / PDF</button>
  </div>
</div>
<?php $data = array_slice($rows, 1); $max = max(1, ...array_map(fn($r) => (int)$r[1], $data)); ?>
<div class="card"><div class="card-header"><i class="bi bi-calendar3 text-primary"></i> Давра: <?= e($label) ?> (<?= e($from) ?> — <?= e($to) ?>)</div>
  <?php foreach ($data as $r): $n = (int)$r[1]; ?>
  <div class="bar-row">
    <div class="d-flex justify-content-between"><span><?= e($r[0]) ?></span><b><?= $n ?></b></div>
    <div class="bar-track"><div class="bar-fill" style="--w:<?= round($n / $max * 100) ?>%"></div></div>
  </div>
  <?php endforeach; ?>
</div>
<?php include 'includes/footer.php'; ?>
