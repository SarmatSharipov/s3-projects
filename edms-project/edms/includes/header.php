<?php
$u = me(); $r = $u['role'] ?? ''; $uid = $u['id'] ?? 0;
$cur = basename($_SERVER['SCRIPT_NAME']);
$titles = [
    'index.php' => 'Саҳифаи асосӣ', 'documents.php' => 'Ҳуҷҷатҳо', 'document_add.php' => 'Бақайдгирии ҳуҷҷат',
    'document_view.php' => 'Ҳуҷҷат', 'tasks.php' => 'Супоришҳо', 'approvals.php' => 'Мувофиқасозӣ',
    'reports.php' => 'Ҳисоботҳо', 'kanban.php' => 'Тахтаи ҳуҷҷатҳо', 'users.php' => 'Корбарон ва шӯъбаҳо', 'backup.php' => 'Нусхаи эҳтиётӣ',
];
$pageTitle = $pageTitle ?? ($titles[$cur] ?? 'EDMS');
$active = in_array($cur, ['document_add.php', 'document_view.php']) ? 'documents.php' : $cur;

// ҳисобкунакҳо барои sidebar
$st = $pdo->prepare("SELECT COUNT(*) FROM approvals a JOIN documents d ON d.id=a.document_id
  WHERE a.user_id=? AND a.status='pending' AND d.status='review'
  AND NOT EXISTS (SELECT 1 FROM approvals b WHERE b.document_id=a.document_id AND b.step_order<a.step_order AND b.status<>'approved')");
$st->execute([$uid]); $navApprovals = (int)$st->fetchColumn();
$st = $pdo->prepare("SELECT COUNT(*) FROM tasks WHERE executor_id=? AND status<>'done' AND deadline<NOW()");
$st->execute([$uid]); $navOverdue = (int)$st->fetchColumn();

function side_link($href, $icon, $label, $active, $count = 0) {
    $cls = $active === $href ? 'side-link active' : 'side-link';
    $badge = $count > 0 ? "<span class='count'>$count</span>" : '';
    echo "<a class='$cls' href='$href'><i class='bi bi-$icon'></i><span>$label</span>$badge</a>";
}
?>
<!doctype html>
<html lang="tg" data-bs-theme="dark">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<meta name="theme-color" content="#070913">
<title><?= e($pageTitle) ?> — EDMS</title>
<script>try{document.documentElement.setAttribute('data-bs-theme',localStorage.getItem('edms-theme')||'dark')}catch(e){}</script>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
<link href="assets/style.css?v=2" rel="stylesheet">
<link href="assets/glass.css?v=4" rel="stylesheet">
<style id="drawer-critical">
@media (max-width: 991.98px) { .side-close { display: inline-grid; position: absolute; top: 14px; right: 14px; width: 42px; height: 42px; z-index: 5; cursor: pointer; } }
@media (max-width: 991.98px) {
  .sidebar {
    width: min(76vw, 300px);
    background: linear-gradient(160deg, rgba(20, 24, 52, .52), rgba(12, 14, 34, .62)) !important;
    backdrop-filter: blur(16px) saturate(170%) !important; -webkit-backdrop-filter: blur(16px) saturate(170%) !important;
    border: 1px solid rgba(255, 255, 255, .16);
  }
  [data-bs-theme="light"] .sidebar {
    background: linear-gradient(160deg, rgba(255, 255, 255, .58), rgba(255, 255, 255, .38)) !important;
    border-color: rgba(255, 255, 255, .85);
  }
  .backdrop-mobile { background: rgba(4, 6, 18, .22); backdrop-filter: none; -webkit-backdrop-filter: none; }
  [data-bs-theme="light"] .backdrop-mobile { background: rgba(40, 30, 60, .12); }
  .side-user { background: rgba(255, 255, 255, .10); backdrop-filter: blur(10px); }
  [data-bs-theme="light"] .side-link { color: #3b3f63; }
  [data-bs-theme="dark"] .side-link { color: #dfe2ff; }
  .side-link.active { color: #fff; }
}
@media (max-width: 991.98px) { .sidebar.open ~ .bottom-nav { opacity: 0; pointer-events: none; transform: translateY(20px); } }
.build-tag { margin-top: auto; text-align: center; font-size: .66rem; opacity: .5; padding: 6px 0; }
.build-tag + .side-user { margin-top: 0; }
</style>
</head>
<body>
<div class="backdrop-mobile"></div>
<aside class="sidebar">
  <a href="index.php" class="brand">
    <span class="brand-logo"><i class="bi bi-file-earmark-text-fill"></i></span>
    <span><b>EDMS</b><small>Гардиши ҳуҷҷатҳо</small></span>
  </a>
  <button type="button" class="icon-btn side-close" data-close aria-label="Пӯшидан"><i class="bi bi-x-lg"></i></button>
  <nav>
    <div class="nav-label">Асосӣ</div>
    <?php
    side_link('index.php', 'grid-1x2-fill', 'Саҳифаи асосӣ', $active);
    side_link('documents.php', 'folder2-open', 'Ҳуҷҷатҳо', $active);
    side_link('kanban.php', 'kanban-fill', 'Тахтаи ҳуҷҷатҳо', $active);
    side_link('tasks.php', 'check2-circle', 'Супоришҳо', $active, $navOverdue);
    side_link('approvals.php', 'patch-check-fill', 'Мувофиқасозӣ', $active, $navApprovals);
    side_link('reports.php', 'bar-chart-line-fill', 'Ҳисоботҳо', $active);
    if ($r === 'admin') {
        echo "<div class='nav-label'>Идоракунӣ</div>";
        side_link('users.php', 'people-fill', 'Корбарон', $active);
        side_link('backup.php', 'cloud-arrow-down-fill', 'Backup', $active);
    }
    ?>
  </nav>
  <div class="build-tag">Нусха 18.11-c</div>
  <div class="side-user">
    <div class="avatar"><?= e(mb_strtoupper(mb_substr($u['full_name'], 0, 1))) ?></div>
    <div class="meta"><b><?= e($u['full_name']) ?></b><small><?= e(ROLES[$r]) ?></small></div>
    <a href="logout.php" class="icon-btn" style="width:36px;height:36px" title="Баромад"><i class="bi bi-box-arrow-right"></i></a>
  </div>
</aside>

<nav class="bottom-nav" aria-label="Навигатсияи ҳамроҳ">
  <a href="index.php" class="<?= $active === 'index.php' ? 'on' : '' ?>"><i class="bi bi-grid-1x2-fill"></i><span>Асосӣ</span></a>
  <a href="documents.php" class="<?= $active === 'documents.php' ? 'on' : '' ?>"><i class="bi bi-folder2-open"></i><span>Ҳуҷҷат</span></a>
  <a href="kanban.php" class="<?= $active === 'kanban.php' ? 'on' : '' ?>"><i class="bi bi-kanban-fill"></i><span>Тахта</span></a>
  <a href="tasks.php" class="<?= $active === 'tasks.php' ? 'on' : '' ?>"><i class="bi bi-check2-circle"></i><span>Супориш</span><?php if ($navOverdue): ?><em><?= $navOverdue ?></em><?php endif; ?></a>
  <button type="button" data-burger><i class="bi bi-list"></i><span>Боз</span></button>
</nav>

<script>
/* Пӯшидани меню — мустақил аз app.js (барои кэши кӯҳна ҳам кор мекунад) */
(function () {
  function close() {
    var sb = document.querySelector('.sidebar'), bd = document.querySelector('.backdrop-mobile');
    if (sb) sb.classList.remove('open'); if (bd) bd.classList.remove('show'); document.body.classList.remove('menu-open');
  }
  function bind(el) {
    if (!el) return; var done = 0;
    var h = function (e) { var t = Date.now(); if (t - done < 400) return; done = t; e.preventDefault(); e.stopPropagation(); close(); };
    el.addEventListener('click', h, true); el.addEventListener('touchend', h, true);
  }
  document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('[data-close]').forEach(bind); bind(document.querySelector('.backdrop-mobile'));
    document.querySelectorAll('.sidebar .side-link').forEach(function (a) { a.addEventListener('click', close); });
    var sb = document.querySelector('.sidebar'), x0 = null;
    if (sb) { sb.addEventListener('touchstart', function (e) { x0 = e.touches[0].clientX; }, { passive: true });
      sb.addEventListener('touchend', function (e) { if (x0 !== null && x0 - e.changedTouches[0].clientX > 60) close(); x0 = null; }, { passive: true }); }
    document.addEventListener('keydown', function (e) { if (e.key === 'Escape') close(); });
  });
})();
</script>
<main class="main">
  <header class="topbar">
    <button class="icon-btn burger" data-burger aria-label="Меню"><i class="bi bi-list"></i></button>
    <h1><?= e($pageTitle) ?></h1>
    <form class="search-box" action="documents.php" method="get">
      <i class="bi bi-search"></i>
      <input name="q" class="form-control" placeholder="Ҷустуҷӯи ҳуҷҷат…" autocomplete="off">
      <kbd>/</kbd>
    </form>
    <button class="icon-btn" data-theme-toggle aria-label="Тема"><i class="bi bi-moon-stars"></i></button>
  </header>

  <?php if ($f = flash()): $c = ['success' => '#10b981', 'danger' => '#ef4444', 'warning' => '#f59e0b'][$f[1]] ?? '#6366f1';
        $ic = $f[1] === 'danger' ? 'x-circle-fill' : 'check-circle-fill'; ?>
  <div class="toast-wrap"><div class="toast-x" style="--c:<?= $c ?>"><i class="bi bi-<?= $ic ?>"></i><span><?= e($f[0]) ?></span></div></div>
  <?php endif; ?>
