<?php
require 'config.php';
require_login();
$role = me()['role']; $uid = me()['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $tid = (int)$_POST['task_id'];
    $file = upload_file('result');
    $st = $pdo->prepare("UPDATE tasks SET status='done', report=?, report_file=COALESCE(?, report_file), done_at=NOW()
                         WHERE id=? AND executor_id=? AND status<>'done'");
    $st->execute([trim($_POST['report']), $file, $tid, $uid]);
    if ($st->rowCount()) {
        // агар ҳамаи супоришҳои ҳуҷҷат иҷро шуда бошанд — ҳуҷҷат "иҷрошуда"
        $pdo->prepare("UPDATE documents d SET status='done' WHERE d.id=(SELECT document_id FROM tasks WHERE id=?)
            AND NOT EXISTS (SELECT 1 FROM tasks t WHERE t.document_id=d.id AND t.status<>'done')")->execute([$tid]);
        flash('Ҳисобот пешниҳод шуд');
    }
    header('Location: tasks.php'); exit;
}

$sql = 'SELECT t.*, d.number, d.subject, u.full_name executor FROM tasks t
        JOIN documents d ON d.id=t.document_id JOIN users u ON u.id=t.executor_id';
$p = [];
if ($role === 'employee') { $sql .= ' WHERE t.executor_id=?'; $p[] = $uid; }
elseif ($role === 'dept_head') { $sql .= ' WHERE u.department_id=?'; $p[] = me()['department_id']; }
$sql .= " ORDER BY (t.status='done'), t.deadline";
$st = $pdo->prepare($sql); $st->execute($p); $tasks = $st->fetchAll();

include 'includes/header.php';
?>
<h4 class="mb-3">Супоришҳо</h4>
<div class="card"><div class="table-responsive">
<table class="table align-middle mb-0">
  <tr><th>Ҳуҷҷат</th><th>Иҷрокунанда</th><th>Мӯҳлат</th><th>Ҳолат</th><th>Амал</th></tr>
  <?php foreach ($tasks as $t): $od = is_overdue($t['deadline'], $t['status']); ?>
  <tr class="<?= $od ? 'table-danger' : ($t['status'] === 'done' ? 'table-success' : '') ?>">
    <td><a href="document_view.php?id=<?= $t['document_id'] ?>"><?= e($t['number']) ?></a><div class="small"><?= e($t['subject']) ?></div></td>
    <td><?= e($t['executor']) ?></td>
    <td><?= e($t['deadline']) ?></td>
    <td><?= $od ? '<span class="badge bg-danger">Мӯҳлатгузашта</span>' : e(TASK_STATUS[$t['status']]) ?></td>
    <td>
      <?php if ($t['executor_id'] == $uid && $t['status'] !== 'done'): ?>
      <form method="post" enctype="multipart/form-data" class="d-flex gap-1 flex-wrap">
        <?= csrf_field() ?><input type="hidden" name="task_id" value="<?= $t['id'] ?>">
        <input name="report" class="form-control form-control-sm" placeholder="Ҳисобот" required style="width:180px">
        <input type="file" name="result" class="form-control form-control-sm" style="width:180px">
        <button class="btn btn-sm btn-success">Анҷом</button>
      </form>
      <?php elseif ($t['report']): ?><span class="small text-muted"><?= e($t['report']) ?></span><?php endif; ?>
    </td>
  </tr>
  <?php endforeach; if (!$tasks) echo '<tr><td colspan="5" class="text-center text-muted">Супориш нест</td></tr>'; ?>
</table></div></div>
<?php include 'includes/footer.php'; ?>
