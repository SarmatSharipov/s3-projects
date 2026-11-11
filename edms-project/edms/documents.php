<?php
require 'config.php';
require_login();

$q = trim($_GET['q'] ?? '');
$number = trim($_GET['number'] ?? '');
$type = $_GET['type'] ?? '';
$from = $_GET['from'] ?? '';
$to = $_GET['to'] ?? '';

$where = []; $p = [];
if ($q !== '')      { $where[] = 'd.subject LIKE ?'; $p[] = "%$q%"; }
if ($number !== '') { $where[] = 'd.number LIKE ?';  $p[] = "%$number%"; }
if (isset(DOC_TYPES[$type])) { $where[] = 'd.type = ?'; $p[] = $type; }
if ($from !== '')   { $where[] = 'd.doc_date >= ?'; $p[] = $from; }
if ($to !== '')     { $where[] = 'd.doc_date <= ?'; $p[] = $to; }

$sql = 'SELECT d.*, u.full_name FROM documents d JOIN users u ON u.id = d.created_by'
     . ($where ? ' WHERE ' . implode(' AND ', $where) : '') . ' ORDER BY d.doc_date DESC, d.id DESC LIMIT 200';
$st = $pdo->prepare($sql); $st->execute($p);
$docs = $st->fetchAll();

include 'includes/header.php';
?>
<div class="d-flex justify-content-between mb-3">
  <h4>Ҳуҷҷатҳо</h4>
  <?php if (in_array(me()['role'], ['chancellery', 'admin'])): ?>
    <a class="btn btn-primary" href="document_add.php">+ Бақайдгирии ҳуҷҷат</a>
  <?php endif; ?>
</div>
<form class="row g-2 mb-3" method="get">
  <div class="col-md-2"><input name="number" class="form-control" placeholder="Рақам" value="<?= e($number) ?>"></div>
  <div class="col-md-3"><input name="q" class="form-control" placeholder="Калимаи калидӣ дар мавзӯъ" value="<?= e($q) ?>"></div>
  <div class="col-md-2"><select name="type" class="form-select">
    <option value="">Ҳама намудҳо</option>
    <?php foreach (DOC_TYPES as $k => $v): ?><option value="<?= $k ?>" <?= $type === $k ? 'selected' : '' ?>><?= $v ?></option><?php endforeach; ?>
  </select></div>
  <div class="col-md-2"><input type="date" name="from" class="form-control" value="<?= e($from) ?>"></div>
  <div class="col-md-2"><input type="date" name="to" class="form-control" value="<?= e($to) ?>"></div>
  <div class="col-md-1"><button class="btn btn-secondary w-100">Ёфтан</button></div>
</form>
<div class="card"><div class="table-responsive">
<table class="table table-hover mb-0">
  <tr><th>№</th><th>Сана</th><th>Намуд</th><th>Мавзӯъ</th><th>Муҳимӣ</th><th>Фиристанда</th><th>Ҳолат</th></tr>
  <?php foreach ($docs as $d): ?>
  <tr>
    <td><a href="document_view.php?id=<?= $d['id'] ?>"><?= e($d['number']) ?></a></td>
    <td><?= e($d['doc_date']) ?></td>
    <td><?= e(DOC_TYPES[$d['type']]) ?></td>
    <td><?= e($d['subject']) ?></td>
    <td><?= $d['importance'] === 'high' ? '<span class="badge bg-danger">Муҳим</span>' : e(IMPORTANCE[$d['importance']]) ?></td>
    <td><?= e($d['sender']) ?></td>
    <td><?= status_badge($d['status']) ?></td>
  </tr>
  <?php endforeach; if (!$docs): ?><tr><td colspan="7" class="text-center text-muted">Ҳуҷҷат ёфт нашуд</td></tr><?php endif; ?>
</table></div></div>
<?php include 'includes/footer.php'; ?>
