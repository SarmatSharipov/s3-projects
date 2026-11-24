<?php
/** @var App\Core\View $view */
$statusLabels = [
    'registered' => 'Бақайдгирифта', 'review' => 'Дар баррасӣ', 'in_progress' => 'Дар иҷро',
    'approved' => 'Тасдиқшуда', 'rejected' => 'Радшуда', 'done' => 'Иҷрошуда',
];
$typeLabels = ['incoming' => 'Воридотӣ', 'outgoing' => 'Содиротӣ', 'internal' => 'Дохилӣ'];
ob_start();
require dirname(__DIR__) . '/partials/notice.php';
?>
<div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
  <h1 class="h3 m-0">Ҳуҷҷатҳо</h1>
  <a class="btn btn-primary" href="/documents/create">+ Бақайдгирии ҳуҷҷат</a>
</div>
<form class="panel mb-3" method="get" action="/documents">
  <div class="row g-3 align-items-end">
    <div class="col-md-4">
      <label class="form-label" for="q">Ҷустуҷӯ</label>
      <input class="form-control" id="q" name="q" type="search" value="<?= $view->e($filters['q']) ?>" placeholder="Рақам, мавзӯъ, фиристанда">
    </div>
    <div class="col-md-2">
      <label class="form-label" for="type">Намуд</label>
      <select class="form-select" id="type" name="type">
        <option value="">Ҳама</option>
        <?php foreach ($typeLabels as $k => $v): ?>
        <option value="<?= $k ?>" <?= $filters['type'] === $k ? 'selected' : '' ?>><?= $v ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="col-md-2">
      <label class="form-label" for="status">Ҳолат</label>
      <select class="form-select" id="status" name="status">
        <option value="">Ҳама</option>
        <?php foreach ($statusLabels as $k => $v): ?>
        <option value="<?= $k ?>" <?= $filters['status'] === $k ? 'selected' : '' ?>><?= $v ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="col-md-2 d-grid">
      <button class="btn btn-primary" type="submit">Ҷустуҷӯ</button>
    </div>
  </div>
</form>
<p class="text-muted-2 small">Ёфт шуд: <?= (int)$result['total'] ?></p>
<div class="panel p-0">
  <div class="table-responsive">
    <table class="table table-hover table-docs table-cards mb-0">
      <thead>
        <tr>
          <th>Рақам</th>
          <th>Сана</th>
          <th>Намуд</th>
          <th>Мавзӯъ</th>
          <th>Фиристанда</th>
          <th>Ҳолат</th>
          <th class="text-end">Амалҳо</th>
        </tr>
      </thead>
      <tbody>
        <?php if ($result['items'] === []): ?>
        <tr><td colspan="7" class="text-center text-muted py-4">Ҳуҷҷат ёфт нашуд</td></tr>
        <?php else: foreach ($result['items'] as $d): ?>
        <tr>
          <td data-label="Рақам"><a href="/documents/<?= (int)$d['id'] ?>"><?= $view->e($d['number']) ?></a></td>
          <td data-label="Сана"><?= $view->e($d['doc_date']) ?></td>
          <td data-label="Намуд"><?= $typeLabels[$d['type']] ?? $d['type'] ?></td>
          <td data-label="Мавзӯъ"><?= $view->e($d['subject']) ?></td>
          <td data-label="Фиристанда"><?= $view->e($d['sender'] ?? '') ?></td>
          <td data-label="Ҳолат"><span class="badge-status st-<?= $view->e($d['status']) ?>"><?= $statusLabels[$d['status']] ?? $d['status'] ?></span></td>
          <td class="text-end text-nowrap" data-label="Амалҳо">
            <a class="btn btn-sm btn-outline-secondary" href="/documents/<?= (int)$d['id'] ?>/edit">Таҳрир</a>
            <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#del<?= (int)$d['id'] ?>">Нест</button>
            <div class="modal fade" id="del<?= (int)$d['id'] ?>" tabindex="-1">
              <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                  <div class="modal-header"><h5 class="modal-title">Ҳуҷҷат нест карда шавад?</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                  <div class="modal-body">Ҳуҷҷати <strong><?= $view->e($d['number'] . ' — ' . $d['subject']) ?></strong> нест карда мешавад.</div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Бекор</button>
                    <form method="post" action="/documents/<?= (int)$d['id'] ?>/delete"><button type="submit" class="btn btn-danger">Нест кардан</button></form>
                  </div>
                </div>
              </div>
            </div>
          </td>
        </tr>
        <?php endforeach; endif; ?>
      </tbody>
    </table>
  </div>
</div>
<?php if ($result['pages'] > 1):
$query = array_filter(['q' => $filters['q'], 'type' => $filters['type'], 'status' => $filters['status']]);
$link = function (int $p) use ($query): string {
    $q = $query; $q['page'] = $p;
    return '/documents?' . http_build_query($q);
};
?>
<nav class="mt-3"><ul class="pagination">
  <li class="page-item <?= $result['page'] <= 1 ? 'disabled' : '' ?>"><a class="page-link" href="<?= $result['page'] <= 1 ? '#' : $link($result['page'] - 1) ?>">Қаблӣ</a></li>
  <?php for ($p = 1; $p <= $result['pages']; $p++): ?>
  <li class="page-item <?= $p === $result['page'] ? 'active' : '' ?>"><a class="page-link" href="<?= $link($p) ?>"><?= $p ?></a></li>
  <?php endfor; ?>
  <li class="page-item <?= $result['page'] >= $result['pages'] ? 'disabled' : '' ?>"><a class="page-link" href="<?= $result['page'] >= $result['pages'] ? '#' : $link($result['page'] + 1) ?>">Баъдӣ</a></li>
</ul></nav>
<?php endif;
$content = ob_get_clean();
require dirname(__DIR__) . '/layout.php';
