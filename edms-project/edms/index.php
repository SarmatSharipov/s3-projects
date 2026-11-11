<?php
require 'config.php';
require_login();
$uid = me()['id']; $role = me()['role'];

$statusCounts = $pdo->query('SELECT status, COUNT(*) c FROM documents GROUP BY status')->fetchAll(PDO::FETCH_KEY_PAIR);
$total = array_sum($statusCounts);
$review = $statusCounts['review'] ?? 0;

$st = $pdo->prepare("SELECT COUNT(*) FROM tasks WHERE executor_id=? AND status<>'done'");
$st->execute([$uid]); $myTasks = (int)$st->fetchColumn();
$st = $pdo->prepare("SELECT COUNT(*) FROM tasks WHERE executor_id=? AND status<>'done' AND deadline<NOW()");
$st->execute([$uid]); $myOverdue = (int)$st->fetchColumn();
$allOverdue = (int)$pdo->query("SELECT COUNT(*) FROM tasks WHERE status<>'done' AND deadline<NOW()")->fetchColumn();
$st = $pdo->prepare("SELECT COUNT(*) FROM approvals a JOIN documents d ON d.id=a.document_id
  WHERE a.user_id=? AND a.status='pending' AND d.status='review'
  AND NOT EXISTS (SELECT 1 FROM approvals b WHERE b.document_id=a.document_id AND b.step_order<a.step_order AND b.status<>'approved')");
$st->execute([$uid]); $myApprovals = (int)$st->fetchColumn();

$last = $pdo->query('SELECT id, number, doc_date, subject, status, type FROM documents ORDER BY id DESC LIMIT 6')->fetchAll();

// вазифаҳои наздиктарин аз рӯи нақш
$sql = "SELECT t.*, d.number, d.subject FROM tasks t JOIN documents d ON d.id=t.document_id JOIN users u ON u.id=t.executor_id WHERE t.status<>'done'";
$p = [];
if ($role === 'employee') { $sql .= ' AND t.executor_id=?'; $p[] = $uid; }
elseif ($role === 'dept_head') { $sql .= ' AND u.department_id=?'; $p[] = me()['department_id']; }
$st = $pdo->prepare($sql . ' ORDER BY t.deadline LIMIT 5'); $st->execute($p); $upcoming = $st->fetchAll();

// ҳалқаи ҳолатҳо
$stops = []; $acc = 0;
foreach (DOC_STATUS as $k => $v) {
    $c = $statusCounts[$k] ?? 0; if (!$c) continue;
    $s = round($acc / $total * 100, 2); $acc += $c; $e = round($acc / $total * 100, 2);
    $stops[] = STATUS_COLOR[$k] . " {$s}% {$e}%";
}
$ring = $total ? 'conic-gradient(' . implode(',', $stops) . ')' : 'conic-gradient(#cbd5e1 0 100%)';

$h = (int)date('G');
$greet = $h < 12 ? 'Субҳ ба хайр' : ($h < 18 ? 'Рӯз ба хайр' : 'Шом ба хайр');
$first = explode(' ', me()['full_name'])[0];

function stat_card($label, $val, $icon, $c1, $c2, $href) {
    echo "<div class='col-6 col-xl-3'><a href='$href' class='card stat spot' style='--c1:$c1;--c2:$c2'>
      <div class='ico'><i class='bi bi-$icon'></i></div>
      <div class='num' data-count='$val'>0</div><div class='lbl'>$label</div></a></div>";
}
include 'includes/header.php';
?>
<div class="card hero mb-4">
  <span class="blob b1"></span><span class="blob b2"></span>
  <div style="position:relative">
    <h2><?= $greet ?>, <?= e($first) ?>! 👋</h2>
    <p>Имрӯз <?= date('d.m.Y') ?>.
      <?php if ($myApprovals): ?>Шумо <b><?= $myApprovals ?></b> ҳуҷҷати интизори тасдиқ доред.
      <?php elseif ($myOverdue): ?>Шумо <b><?= $myOverdue ?></b> супориши мӯҳлатгузашта доред.
      <?php else: ?>Ҳама чиз тибқи нақша пеш меравад ✨<?php endif; ?></p>
    <div class="mt-3 d-flex gap-2 flex-wrap">
      <?php if (in_array($role, ['chancellery', 'admin'])): ?>
        <a href="document_add.php" class="btn btn-light"><i class="bi bi-plus-lg me-1"></i>Ҳуҷҷати нав</a>
      <?php endif; ?>
      <a href="documents.php" class="btn btn-light"><i class="bi bi-folder2-open me-1"></i>Ҳамаи ҳуҷҷатҳо</a>
    </div>
  </div>
</div>

<div class="row g-4 mb-4">
  <?php
  stat_card('Ҳамаи ҳуҷҷатҳо', $total, 'files', '#6366f1', '#8b5cf6', 'documents.php');
  stat_card('Дар баррасӣ', $review, 'hourglass-split', '#f59e0b', '#f97316', 'documents.php');
  stat_card('Супоришҳои ман', $myTasks, 'list-task', '#0ea5e9', '#22d3ee', 'tasks.php');
  stat_card($role === 'employee' ? 'Мӯҳлатгузашта' : 'Мӯҳлатгузашта (ҳама)', $role === 'employee' ? $myOverdue : $allOverdue, 'alarm-fill', '#ef4444', '#f43f5e', 'tasks.php');
  ?>
</div>

<div class="row g-4">
  <div class="col-lg-7">
    <div class="card h-100">
      <div class="card-header"><i class="bi bi-clock-history text-primary"></i> Ҳуҷҷатҳои охирин
        <a href="documents.php" class="ms-auto small">Ҳама →</a></div>
      <?php foreach ($last as $d): ?>
      <a class="list-item" href="document_view.php?id=<?= $d['id'] ?>" style="--c:<?= STATUS_COLOR[$d['status']] ?>">
        <span class="dot"><i class="bi bi-<?= ['incoming' => 'box-arrow-in-down', 'outgoing' => 'box-arrow-up', 'internal' => 'arrow-left-right'][$d['type']] ?>"></i></span>
        <div class="flex-grow-1" style="min-width:0">
          <div class="t text-truncate"><?= e($d['subject']) ?></div>
          <small>№ <?= e($d['number']) ?> · <?= e($d['doc_date']) ?></small>
        </div>
        <?= status_badge($d['status']) ?>
      </a>
      <?php endforeach; if (!$last): ?><div class="p-4 text-center text-muted">Ҳоло ҳуҷҷат нест</div><?php endif; ?>
    </div>
  </div>

  <div class="col-lg-5">
    <div class="card mb-4">
      <div class="card-header"><i class="bi bi-pie-chart-fill text-primary"></i> Ҳолати ҳуҷҷатҳо</div>
      <div class="card-body">
        <div class="ring" style="background:<?= $ring ?>"><div class="center"><b data-count="<?= $total ?>">0</b><small>ҳуҷҷат</small></div></div>
        <div class="mt-4">
          <?php foreach (DOC_STATUS as $k => $v): ?>
          <div class="legend" style="--c:<?= STATUS_COLOR[$k] ?>"><i></i><?= e($v) ?><b><?= (int)($statusCounts[$k] ?? 0) ?></b></div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>

    <div class="card">
      <div class="card-header"><i class="bi bi-alarm text-danger"></i> Мӯҳлатҳои наздик</div>
      <?php foreach ($upcoming as $t): $od = is_overdue($t['deadline'], $t['status']); ?>
      <a class="list-item" href="document_view.php?id=<?= $t['document_id'] ?>" style="--c:<?= $od ? '#ef4444' : '#0ea5e9' ?>">
        <span class="dot"><i class="bi bi-<?= $od ? 'exclamation-triangle-fill' : 'clock' ?>"></i></span>
        <div style="min-width:0"><div class="t text-truncate"><?= e($t['subject']) ?></div>
          <small style="<?= $od ? 'color:#ef4444;font-weight:700' : '' ?>"><?= e(time_left($t['deadline'])) ?></small></div>
      </a>
      <?php endforeach; if (!$upcoming): ?><div class="p-4 text-center text-muted">Супориши кушода нест 🎉</div><?php endif; ?>
    </div>
  </div>
</div>
<?php include 'includes/footer.php'; ?>
