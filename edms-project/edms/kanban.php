<?php
require 'config.php';
require_login();

$rows = $pdo->query("SELECT d.id, d.number, d.doc_date, d.subject, d.type, d.importance, d.sender, d.status,
  (SELECT COUNT(*) FROM approvals a WHERE a.document_id = d.id) AS ap_total,
  (SELECT COUNT(*) FROM approvals a WHERE a.document_id = d.id AND a.status = 'approved') AS ap_ok,
  (SELECT COUNT(*) FROM tasks t WHERE t.document_id = d.id) AS t_total,
  (SELECT COUNT(*) FROM tasks t WHERE t.document_id = d.id AND t.status = 'done') AS t_done,
  (SELECT COUNT(*) FROM tasks t WHERE t.document_id = d.id AND t.status <> 'done' AND t.deadline < NOW()) AS t_over
  FROM documents d ORDER BY d.id DESC LIMIT 300")->fetchAll();

$cols = array_fill_keys(array_keys(DOC_STATUS), []);
foreach ($rows as $r) $cols[$r['status']][] = $r;
$total = count($rows);
$overdue = array_sum(array_column($rows, 't_over'));
$typeIcon = ['incoming' => 'box-arrow-in-down', 'outgoing' => 'box-arrow-up', 'internal' => 'arrow-left-right'];

function initials($s) {
    $w = preg_split('/\s+/u', trim((string)$s)); $o = '';
    foreach (array_slice($w, 0, 2) as $x) $o .= mb_strtoupper(mb_substr($x, 0, 1));
    return $o ?: '—';
}
include 'includes/header.php';
?>
<div class="board-top">
  <div class="mini-stat"><span>Ҳамаи ҳуҷҷатҳо</span><b data-count="<?= $total ?>">0</b></div>
  <div class="mini-stat"><span>Дар баррасӣ</span><b data-count="<?= count($cols['review']) ?>">0</b></div>
  <div class="mini-stat"><span>Дар иҷро</span><b data-count="<?= count($cols['in_progress']) ?>">0</b></div>
  <div class="mini-stat danger"><span>Мӯҳлатгузашта</span><b data-count="<?= (int)$overdue ?>">0</b></div>
  <div class="chips-filter" role="group" aria-label="Филтр">
    <button class="on" data-f="all">Ҳама</button>
    <?php foreach (DOC_TYPES as $k => $v): ?><button data-f="<?= $k ?>"><?= e($v) ?></button><?php endforeach; ?>
  </div>
</div>

<div class="board">
<?php foreach (DOC_STATUS as $k => $label): $list = $cols[$k]; ?>
  <section class="col-k" style="--c:<?= STATUS_COLOR[$k] ?>">
    <header><i></i><b><?= e($label) ?></b><span class="n"><?= count($list) ?></span></header>
    <div class="col-body">
    <?php foreach ($list as $d):
        $pct = null;
        if ($d['status'] === 'review' && $d['ap_total'])        $pct = round($d['ap_ok'] / $d['ap_total'] * 100);
        elseif ($d['status'] === 'in_progress' && $d['t_total']) $pct = round($d['t_done'] / $d['t_total'] * 100);
    ?>
      <a class="k-card" href="document_view.php?id=<?= $d['id'] ?>" data-type="<?= e($d['type']) ?>">
        <div class="k-head">
          <span class="k-title"><?= e($d['subject']) ?></span>
          <?php if ($d['importance'] === 'high'): ?><span class="k-hot" title="Муҳим"><i class="bi bi-fire"></i></span><?php endif; ?>
        </div>
        <div class="k-meta">
          <span><i class="bi bi-<?= $typeIcon[$d['type']] ?>"></i> <?= e(DOC_TYPES[$d['type']]) ?></span>
          <span>№ <?= e($d['number']) ?></span>
        </div>
        <?php if ($pct !== null): ?>
          <div class="k-prog"><i style="width:<?= $pct ?>%"></i></div>
          <div class="k-sub"><?= $d['status'] === 'review' ? "Мувофиқа {$d['ap_ok']}/{$d['ap_total']}" : "Супоришҳо {$d['t_done']}/{$d['t_total']}" ?></div>
        <?php endif; ?>
        <div class="k-foot">
          <span class="k-av"><?= e(initials($d['sender'])) ?></span>
          <small class="text-truncate"><?= e($d['sender'] ?: '—') ?></small>
          <?php if ($d['t_over']): ?><span class="k-over"><i class="bi bi-alarm-fill"></i> <?= (int)$d['t_over'] ?></span>
          <?php else: ?><small class="ms-auto"><?= e($d['doc_date']) ?></small><?php endif; ?>
        </div>
      </a>
    <?php endforeach; if (!$list): ?><div class="k-empty">Холӣ</div><?php endif; ?>
    </div>
  </section>
<?php endforeach; ?>
</div>
<?php include 'includes/footer.php'; ?>
