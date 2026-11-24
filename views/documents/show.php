<?php
/** @var App\Core\View $view */
$statusLabels = [
    'registered' => 'Бақайдгирифта', 'review' => 'Дар баррасӣ', 'in_progress' => 'Дар иҷро',
    'approved' => 'Тасдиқшуда', 'rejected' => 'Радшуда', 'done' => 'Иҷрошуда',
];
$typeLabels = ['incoming' => 'Воридотӣ', 'outgoing' => 'Содиротӣ', 'internal' => 'Дохилӣ'];
$impLabels = ['low' => 'Паст', 'normal' => 'Муқаррарӣ', 'high' => 'Муҳим'];
ob_start();
require dirname(__DIR__) . '/partials/notice.php';
?>
<nav aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="/documents">Ҳуҷҷатҳо</a></li>
    <li class="breadcrumb-item active"><?= $view->e($doc['number']) ?></li>
  </ol>
</nav>
<article class="panel doc-card">
  <header class="doc-card__head">
    <h1 class="h3 mb-2"><?= $view->e($doc['subject']) ?></h1>
    <span class="badge-status st-<?= $view->e($doc['status']) ?>"><?= $statusLabels[$doc['status']] ?? $doc['status'] ?></span>
  </header>
  <div class="doc-card__actions">
    <a class="btn btn-outline-secondary" href="/documents/<?= (int)$doc['id'] ?>/edit">Таҳрир</a>
    <button class="btn btn-outline-danger" type="button" data-bs-toggle="modal" data-bs-target="#confirm-delete">Нест кардан</button>
  </div>
  <dl class="doc-card__meta meta-list">
    <div><dt>Рақам</dt><dd><?= $view->e($doc['number']) ?></dd></div>
    <div><dt>Намуд</dt><dd><?= $typeLabels[$doc['type']] ?? $doc['type'] ?></dd></div>
    <div><dt>Фиристанда</dt><dd><?= $view->e($doc['sender'] ?? '—') ?></dd></div>
    <div><dt>Сана</dt><dd><?= $view->e($doc['doc_date']) ?></dd></div>
    <div><dt>Муҳимӣ</dt><dd><?= $impLabels[$doc['importance']] ?? $doc['importance'] ?></dd></div>
    <div><dt>Қабулкунанда</dt><dd><?= $view->e($doc['receiver'] ?? '—') ?></dd></div>
    <div><dt>Эҷодкунанда</dt><dd><?= $view->e($doc['creator_name'] ?? '') ?></dd></div>
  </dl>
</article>
<div class="modal fade" id="confirm-delete" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header"><h5 class="modal-title">Ҳуҷҷат нест карда шавад?</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
      <div class="modal-body">Ҳуҷҷати <strong><?= $view->e($doc['number'] . ' — ' . $doc['subject']) ?></strong> нест карда мешавад.</div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Бекор</button>
        <form method="post" action="/documents/<?= (int)$doc['id'] ?>/delete"><button type="submit" class="btn btn-danger">Нест кардан</button></form>
      </div>
    </div>
  </div>
</div>
<?php
$content = ob_get_clean();
require dirname(__DIR__) . '/layout.php';
