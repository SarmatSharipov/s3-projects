<?php
/** @var App\Core\View $view */
$action = $id ? '/documents/' . $id . '/edit' : '/documents/create';
$val = fn (string $field): string => $view->e($doc[$field] ?? '');
$invalid = fn (string $field): string => isset($errors[$field]) ? ' is-invalid' : '';
$error = function (string $field) use ($errors, $view): string {
    return isset($errors[$field]) ? '<div class="invalid-feedback">' . $view->e($errors[$field]) . '</div>' : '';
};
$typeLabels = ['incoming' => 'Воридотӣ', 'outgoing' => 'Содиротӣ', 'internal' => 'Дохилӣ'];
$impLabels = ['low' => 'Паст', 'normal' => 'Муқаррарӣ', 'high' => 'Муҳим'];
$statusLabels = [
    'registered' => 'Бақайдгирифта', 'review' => 'Дар баррасӣ', 'in_progress' => 'Дар иҷро',
    'approved' => 'Тасдиқшуда', 'rejected' => 'Радшуда', 'done' => 'Иҷрошуда',
];
ob_start();
?>
<nav aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="/documents">Ҳуҷҷатҳо</a></li>
    <li class="breadcrumb-item active"><?= $id ? 'Таҳрир' : 'Бақайдгирӣ' ?></li>
  </ol>
</nav>
<h1 class="h3 mb-3"><?= $view->e($title) ?></h1>
<form class="panel" method="post" action="<?= $view->url($action) ?>" novalidate>
  <div class="row g-3">
    <div class="col-md-3">
      <label class="form-label required" for="number">Рақам</label>
      <input class="form-control<?= $invalid('number') ?>" id="number" name="number" required value="<?= $val('number') ?>">
      <?= $error('number') ?>
    </div>
    <div class="col-md-3">
      <label class="form-label required" for="doc_date">Сана</label>
      <input class="form-control<?= $invalid('doc_date') ?>" id="doc_date" name="doc_date" type="date" required value="<?= $val('doc_date') ?>">
      <?= $error('doc_date') ?>
    </div>
    <div class="col-md-3">
      <label class="form-label required" for="type">Намуд</label>
      <select class="form-select<?= $invalid('type') ?>" id="type" name="type" required>
        <option value="">Интихоб</option>
        <?php foreach ($typeLabels as $k => $v): ?>
        <option value="<?= $k ?>" <?= ($doc['type'] ?? '') === $k ? 'selected' : '' ?>><?= $v ?></option>
        <?php endforeach; ?>
      </select>
      <?= $error('type') ?>
    </div>
    <div class="col-md-3">
      <label class="form-label" for="importance">Муҳимӣ</label>
      <select class="form-select<?= $invalid('importance') ?>" id="importance" name="importance">
        <?php foreach ($impLabels as $k => $v): ?>
        <option value="<?= $k ?>" <?= ($doc['importance'] ?? 'normal') === $k ? 'selected' : '' ?>><?= $v ?></option>
        <?php endforeach; ?>
      </select>
      <?= $error('importance') ?>
    </div>
    <div class="col-12">
      <label class="form-label required" for="subject">Мавзӯъ</label>
      <input class="form-control<?= $invalid('subject') ?>" id="subject" name="subject" required minlength="4" maxlength="255" value="<?= $val('subject') ?>">
      <?= $error('subject') ?>
    </div>
    <div class="col-md-6">
      <label class="form-label" for="sender">Фиристанда</label>
      <input class="form-control<?= $invalid('sender') ?>" id="sender" name="sender" value="<?= $val('sender') ?>">
      <?= $error('sender') ?>
    </div>
    <div class="col-md-6">
      <label class="form-label" for="receiver">Қабулкунанда</label>
      <input class="form-control<?= $invalid('receiver') ?>" id="receiver" name="receiver" value="<?= $val('receiver') ?>">
      <?= $error('receiver') ?>
    </div>
    <div class="col-md-4">
      <label class="form-label" for="status">Ҳолат</label>
      <select class="form-select<?= $invalid('status') ?>" id="status" name="status">
        <?php foreach ($statusLabels as $k => $v): ?>
        <option value="<?= $k ?>" <?= ($doc['status'] ?? 'registered') === $k ? 'selected' : '' ?>><?= $v ?></option>
        <?php endforeach; ?>
      </select>
      <?= $error('status') ?>
    </div>
  </div>
  <div class="d-flex gap-2 mt-4">
    <button class="btn btn-primary" type="submit">Сабт кардан</button>
    <a class="btn btn-outline-secondary" href="/documents">Бекор кардан</a>
  </div>
</form>
<?php
$content = ob_get_clean();
require dirname(__DIR__) . '/layout.php';
