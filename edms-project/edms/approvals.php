<?php
require 'config.php';
require_login();
$uid = me()['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $aid = (int)$_POST['approval_id'];
    $decision = $_POST['decision'] === 'approve' ? 'approved' : 'rejected';
    $comment = trim($_POST['comment'] ?? '');
    if ($decision === 'rejected' && $comment === '') { flash('Сабаби радкуниро нависед', 'danger'); header('Location: approvals.php'); exit; }

    // танҳо қадами ҷорӣ (пайдарпай) иҷозат дода мешавад
    $chk = $pdo->prepare("SELECT a.document_id FROM approvals a WHERE a.id=? AND a.user_id=? AND a.status='pending'
        AND NOT EXISTS (SELECT 1 FROM approvals b WHERE b.document_id=a.document_id AND b.step_order<a.step_order AND b.status<>'approved')");
    $chk->execute([$aid, $uid]); $docId = $chk->fetchColumn();
    if ($docId) {
        $pdo->prepare('UPDATE approvals SET status=?, comment=?, decided_at=NOW() WHERE id=?')->execute([$decision, $comment, $aid]);
        if ($decision === 'rejected') {
            $pdo->prepare("UPDATE documents SET status='rejected' WHERE id=?")->execute([$docId]);
        } else {
            $left = $pdo->prepare("SELECT COUNT(*) FROM approvals WHERE document_id=? AND status<>'approved'");
            $left->execute([$docId]);
            if (!$left->fetchColumn()) $pdo->prepare("UPDATE documents SET status='approved' WHERE id=?")->execute([$docId]);
        }
        flash($decision === 'approved' ? 'Ҳуҷҷат тасдиқ шуд' : 'Ҳуҷҷат баргардонида шуд');
    }
    header('Location: approvals.php'); exit;
}

$st = $pdo->prepare("SELECT a.*, d.number, d.subject FROM approvals a JOIN documents d ON d.id=a.document_id
  WHERE a.user_id=? AND a.status='pending' AND d.status='review'
  AND NOT EXISTS (SELECT 1 FROM approvals b WHERE b.document_id=a.document_id AND b.step_order<a.step_order AND b.status<>'approved')
  ORDER BY a.id");
$st->execute([$uid]); $items = $st->fetchAll();

$hist = $pdo->prepare("SELECT a.*, d.number, d.subject FROM approvals a JOIN documents d ON d.id=a.document_id
  WHERE a.user_id=? AND a.status<>'pending' ORDER BY a.decided_at DESC LIMIT 20");
$hist->execute([$uid]); $history = $hist->fetchAll();

include 'includes/header.php';
?>
<h4 class="mb-3">Мувофиқасозӣ</h4>
<?php foreach ($items as $a): ?>
<div class="card mb-3"><div class="card-body">
  <h6><a href="document_view.php?id=<?= $a['document_id'] ?>">№ <?= e($a['number']) ?></a> — <?= e($a['subject']) ?>
    <span class="badge bg-secondary">Қадами <?= $a['step_order'] ?></span></h6>
  <form method="post" class="row g-2"><?= csrf_field() ?><input type="hidden" name="approval_id" value="<?= $a['id'] ?>">
    <div class="col-md-7"><input name="comment" class="form-control" placeholder="Шарҳ (барои радкунӣ ҳатмӣ)"></div>
    <div class="col-md-5 d-flex gap-2">
      <button name="decision" value="approve" class="btn btn-success">Тасдиқ кардан</button>
      <button name="decision" value="reject" class="btn btn-danger">Баргардонидан</button>
    </div>
  </form>
</div></div>
<?php endforeach; if (!$items) echo '<div class="alert alert-info">Ҳуҷҷати интизори тасдиқи шумо нест</div>'; ?>

<?php if ($history): ?>
<div class="card"><div class="card-header">Таърихи қарорҳои ман</div>
<table class="table mb-0"><?php foreach ($history as $h): ?>
  <tr><td>№ <?= e($h['number']) ?></td><td><?= e($h['subject']) ?></td>
  <td><?= $h['status'] === 'approved' ? 'Тасдиқ' : 'Баргардонида' ?></td><td><?= e($h['decided_at']) ?></td><td><?= e($h['comment']) ?></td></tr>
<?php endforeach; ?></table></div>
<?php endif; ?>
<?php include 'includes/footer.php'; ?>
