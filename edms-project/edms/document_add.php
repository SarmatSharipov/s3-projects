<?php
require 'config.php';
require_role(['chancellery', 'admin']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $type = $_POST['type'] ?? '';
    $imp = $_POST['importance'] ?? 'normal';
    if (!isset(DOC_TYPES[$type]) || !isset(IMPORTANCE[$imp])) { flash('Маълумот нодуруст', 'danger'); header('Location: document_add.php'); exit; }
    $file = upload_file('scan');
    $st = $pdo->prepare('INSERT INTO documents (number, doc_date, subject, type, importance, sender, receiver, scan_file, created_by)
                         VALUES (?,?,?,?,?,?,?,?,?)');
    $st->execute([trim($_POST['number']), $_POST['doc_date'], trim($_POST['subject']), $type, $imp,
                  trim($_POST['sender']), trim($_POST['receiver']), $file, me()['id']]);
    flash('Ҳуҷҷат бақайд гирифта шуд');
    header('Location: document_view.php?id=' . $pdo->lastInsertId()); exit;
}

$next = $pdo->query('SELECT COALESCE(MAX(id),0)+1 FROM documents')->fetchColumn();
include 'includes/header.php';
?>
<h4 class="mb-3">Бақайдгирии ҳуҷҷати нав</h4>
<form method="post" enctype="multipart/form-data" class="card card-body">
  <?= csrf_field() ?>
  <div class="row g-3">
    <div class="col-md-3"><label class="form-label">Рақами тартибӣ</label>
      <input name="number" class="form-control" required value="<?= date('Y') . '-' . str_pad($next, 4, '0', STR_PAD_LEFT) ?>"></div>
    <div class="col-md-3"><label class="form-label">Санаи воридшавӣ</label>
      <input type="date" name="doc_date" class="form-control" required value="<?= date('Y-m-d') ?>"></div>
    <div class="col-md-3"><label class="form-label">Намуд</label>
      <select name="type" class="form-select"><?php foreach (DOC_TYPES as $k => $v): ?><option value="<?= $k ?>"><?= $v ?></option><?php endforeach; ?></select></div>
    <div class="col-md-3"><label class="form-label">Муҳимӣ</label>
      <select name="importance" class="form-select"><?php foreach (IMPORTANCE as $k => $v): ?><option value="<?= $k ?>" <?= $k === 'normal' ? 'selected' : '' ?>><?= $v ?></option><?php endforeach; ?></select></div>
    <div class="col-12"><label class="form-label">Мавзӯъ</label><input name="subject" class="form-control" required></div>
    <div class="col-md-6"><label class="form-label">Фиристанда</label><input name="sender" class="form-control"></div>
    <div class="col-md-6"><label class="form-label">Қабулкунанда</label><input name="receiver" class="form-control"></div>
    <div class="col-12"><label class="form-label">Файли сканшуда (PDF, DOC, DOCX, JPG)</label>
      <input type="file" name="scan" class="form-control" accept=".pdf,.doc,.docx,.jpg,.jpeg"></div>
  </div>
  <div class="mt-3"><button class="btn btn-primary">Захира кардан</button>
    <a href="documents.php" class="btn btn-link">Бекор</a></div>
</form>
<?php include 'includes/footer.php'; ?>
