<?php
require 'config.php';
require_login();
$id = (int)($_GET['id'] ?? 0);
$role = me()['role']; $uid = me()['id'];

$st = $pdo->prepare('SELECT d.*, u.full_name creator FROM documents d JOIN users u ON u.id=d.created_by WHERE d.id=?');
$st->execute([$id]); $doc = $st->fetch();
if (!$doc) { http_response_code(404); exit('Ҳуҷҷат ёфт нашуд'); }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $action = $_POST['action'] ?? '';

    if ($action === 'resolution' && in_array($role, ['director', 'admin'])) {
        $exec = (int)$_POST['executor_id'];
        $pdo->beginTransaction();
        $pdo->prepare('INSERT INTO resolutions (document_id, director_id, text) VALUES (?,?,?)')
            ->execute([$id, $uid, trim($_POST['text'])]);
        $rid = $pdo->lastInsertId();
        $pdo->prepare('INSERT INTO tasks (document_id, resolution_id, executor_id, assigned_by, deadline) VALUES (?,?,?,?,?)')
            ->execute([$id, $rid, $exec, $uid, str_replace('T', ' ', $_POST['deadline'])]);
        $pdo->prepare("UPDATE documents SET status='in_progress' WHERE id=? AND status IN ('registered','review','approved')")->execute([$id]);
        $pdo->commit();
        flash('Резолютсия сабт шуд ва супориш таъин гардид');
    }

    if ($action === 'assign' && in_array($role, ['dept_head', 'admin'])) {
        $pdo->prepare('INSERT INTO tasks (document_id, executor_id, assigned_by, deadline) VALUES (?,?,?,?)')
            ->execute([$id, (int)$_POST['executor_id'], $uid, str_replace('T', ' ', $_POST['deadline'])]);
        flash('Супориш ба корманд таъин шуд');
    }

    if ($action === 'add_approver' && in_array($role, ['chancellery', 'director', 'admin'])) {
        $step = $pdo->prepare('SELECT COALESCE(MAX(step_order),0)+1 FROM approvals WHERE document_id=?');
        $step->execute([$id]);
        $pdo->prepare('INSERT INTO approvals (document_id, user_id, step_order) VALUES (?,?,?)')
            ->execute([$id, (int)$_POST['user_id'], $step->fetchColumn()]);
        $pdo->prepare("UPDATE documents SET status='review' WHERE id=? AND status IN ('registered','approved','rejected')")->execute([$id]);
        flash('Мувофиқкунанда ба масир илова шуд');
    }

    if ($action === 'close' && in_array($role, ['director', 'admin'])) {
        $pdo->prepare("UPDATE documents SET status='done' WHERE id=?")->execute([$id]);
        flash('Ҳуҷҷат иҷрошуда эълон шуд');
    }
    header('Location: document_view.php?id=' . $id); exit;
}

$res = $pdo->prepare('SELECT r.*, u.full_name FROM resolutions r JOIN users u ON u.id=r.director_id WHERE document_id=? ORDER BY r.id');
$res->execute([$id]); $resolutions = $res->fetchAll();

$tk = $pdo->prepare('SELECT t.*, u.full_name FROM tasks t JOIN users u ON u.id=t.executor_id WHERE document_id=? ORDER BY t.deadline');
$tk->execute([$id]); $tasks = $tk->fetchAll();

$ap = $pdo->prepare('SELECT a.*, u.full_name FROM approvals a JOIN users u ON u.id=a.user_id WHERE document_id=? ORDER BY step_order');
$ap->execute([$id]); $approvals = $ap->fetchAll();

$directors = $pdo->query("SELECT id, full_name FROM users WHERE role IN ('employee','dept_head') ORDER BY full_name")->fetchAll();
$allUsers  = $pdo->query('SELECT id, full_name FROM users ORDER BY full_name')->fetchAll();
if ($role === 'dept_head') {
    $s = $pdo->prepare("SELECT id, full_name FROM users WHERE role='employee' AND department_id=?");
    $s->execute([me()['department_id']]); $team = $s->fetchAll();
} else { $team = $directors; }

include 'includes/header.php';
?>
<a href="documents.php">&larr; Ба рӯйхат</a>
<div class="card my-3"><div class="card-body">
  <div class="d-flex justify-content-between">
    <h5>№ <?= e($doc['number']) ?> — <?= e($doc['subject']) ?></h5>
    <span class="align-self-start"><?= status_badge($doc['status']) ?></span>
  </div>
  <div class="row small text-muted">
    <div class="col-md-3">Сана: <?= e($doc['doc_date']) ?></div>
    <div class="col-md-3">Намуд: <?= e(DOC_TYPES[$doc['type']]) ?></div>
    <div class="col-md-3">Муҳимӣ: <?= e(IMPORTANCE[$doc['importance']]) ?></div>
    <div class="col-md-3">Бақайдгиранда: <?= e($doc['creator']) ?></div>
    <div class="col-md-6">Фиристанда: <?= e($doc['sender']) ?></div>
    <div class="col-md-6">Қабулкунанда: <?= e($doc['receiver']) ?></div>
  </div>
  <?php if ($doc['scan_file']): ?>
    <a class="btn btn-sm btn-outline-primary mt-2" target="_blank" href="uploads/<?= e($doc['scan_file']) ?>">Файли сканшуда</a>
  <?php endif; ?>
  <?php if (in_array($role, ['director', 'admin']) && $doc['status'] !== 'done'): ?>
    <form method="post" class="d-inline"><?= csrf_field() ?><input type="hidden" name="action" value="close">
      <button class="btn btn-sm btn-success mt-2">Иҷрошуда эълон кардан</button></form>
  <?php endif; ?>
</div></div>

<div class="row g-3">
<div class="col-lg-6">
  <div class="card mb-3"><div class="card-header">Резолютсияҳо</div><div class="card-body">
    <?php foreach ($resolutions as $r): ?>
      <div class="border-bottom pb-2 mb-2"><div class="small text-muted"><?= e($r['full_name']) ?> · <?= e($r['created_at']) ?></div><?= nl2br(e($r['text'])) ?></div>
    <?php endforeach; if (!$resolutions) echo '<span class="text-muted">Резолютсия нест</span>'; ?>
    <?php if (in_array($role, ['director', 'admin'])): ?>
    <hr><form method="post"><?= csrf_field() ?><input type="hidden" name="action" value="resolution">
      <textarea name="text" class="form-control mb-2" rows="3" placeholder="Матни резолютсия" required></textarea>
      <div class="row g-2">
        <div class="col-md-6"><select name="executor_id" class="form-select" required>
          <option value="">Иҷрокунанда</option><?php foreach ($directors as $u): ?><option value="<?= $u['id'] ?>"><?= e($u['full_name']) ?></option><?php endforeach; ?></select></div>
        <div class="col-md-6"><input type="datetime-local" name="deadline" class="form-control" required></div>
      </div>
      <button class="btn btn-primary btn-sm mt-2">Сабт ва таъини иҷрокунанда</button>
    </form>
    <?php endif; ?>
  </div></div>

  <div class="card"><div class="card-header">Масири мувофиқа</div><div class="card-body">
    <?php foreach ($approvals as $a): $c = ['pending' => 'secondary', 'approved' => 'success', 'rejected' => 'danger'][$a['status']]; ?>
      <div class="mb-2"><span class="badge bg-<?= $c ?>">Қадами <?= $a['step_order'] ?></span>
        <?= e($a['full_name']) ?> —
        <?= ['pending' => 'интизор', 'approved' => 'тасдиқ кард', 'rejected' => 'баргардонд'][$a['status']] ?>
        <?php if ($a['comment']): ?><div class="small text-muted ms-2"><?= e($a['comment']) ?></div><?php endif; ?>
      </div>
    <?php endforeach; if (!$approvals) echo '<span class="text-muted">Масир таъин нашудааст</span>'; ?>
    <?php if (in_array($role, ['chancellery', 'director', 'admin'])): ?>
    <hr><form method="post" class="row g-2"><?= csrf_field() ?><input type="hidden" name="action" value="add_approver">
      <div class="col-8"><select name="user_id" class="form-select" required>
        <option value="">Илова кардани мувофиқкунанда</option>
        <?php foreach ($allUsers as $u): ?><option value="<?= $u['id'] ?>"><?= e($u['full_name']) ?></option><?php endforeach; ?></select></div>
      <div class="col-4"><button class="btn btn-outline-primary w-100">Илова</button></div>
    </form>
    <?php endif; ?>
  </div></div>
</div>

<div class="col-lg-6">
  <div class="card"><div class="card-header">Супоришҳо</div><div class="card-body">
    <?php foreach ($tasks as $t): $od = is_overdue($t['deadline'], $t['status']); ?>
      <div class="border rounded p-2 mb-2 <?= $od ? 'border-danger bg-danger-subtle' : '' ?>">
        <strong><?= e($t['full_name']) ?></strong>
        <span class="badge bg-<?= $t['status'] === 'done' ? 'success' : ($od ? 'danger' : 'info') ?>"><?= $od ? 'Мӯҳлатгузашта' : e(TASK_STATUS[$t['status']]) ?></span>
        <div class="small">Мӯҳлат: <?= e($t['deadline']) ?></div>
        <?php if ($t['report']): ?><div class="small mt-1">Ҳисобот: <?= nl2br(e($t['report'])) ?></div><?php endif; ?>
        <?php if ($t['report_file']): ?><a class="small" target="_blank" href="uploads/<?= e($t['report_file']) ?>">Файли натиҷавӣ</a><?php endif; ?>
      </div>
    <?php endforeach; if (!$tasks) echo '<span class="text-muted">Супориш нест</span>'; ?>
    <?php if (in_array($role, ['dept_head', 'admin'])): ?>
    <hr><form method="post" class="row g-2"><?= csrf_field() ?><input type="hidden" name="action" value="assign">
      <div class="col-md-5"><select name="executor_id" class="form-select" required>
        <option value="">Корманд</option><?php foreach ($team as $u): ?><option value="<?= $u['id'] ?>"><?= e($u['full_name']) ?></option><?php endforeach; ?></select></div>
      <div class="col-md-5"><input type="datetime-local" name="deadline" class="form-control" required></div>
      <div class="col-md-2"><button class="btn btn-outline-primary w-100">Таъин</button></div>
    </form>
    <?php endif; ?>
  </div></div>
</div>
</div>
<?php include 'includes/footer.php'; ?>
