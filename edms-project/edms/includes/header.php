<?php
$u = me(); $r = $u['role'] ?? ''; $uid = $u['id'] ?? 0;
$cur = basename($_SERVER['SCRIPT_NAME']);
$titles = [
    'index.php' => 'Саҳифаи асосӣ', 'documents.php' => 'Ҳуҷҷатҳо', 'document_add.php' => 'Бақайдгирии ҳуҷҷат',
    'document_view.php' => 'Ҳуҷҷат', 'tasks.php' => 'Супоришҳо', 'approvals.php' => 'Мувофиқасозӣ',
    'reports.php' => 'Ҳисоботҳо', 'users.php' => 'Корбарон ва шӯъбаҳо', 'backup.php' => 'Нусхаи эҳтиётӣ',
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
<html lang="tg" data-bs-theme="light">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($pageTitle) ?> — EDMS</title>
<script>try{document.documentElement.setAttribute('data-bs-theme',localStorage.getItem('theme')||(matchMedia('(prefers-color-scheme: dark)').matches?'dark':'light'))}catch(e){}</script>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
<link href="assets/style.css?v=2" rel="stylesheet">
</head>
<body>
<div class="backdrop-mobile"></div>
<aside class="sidebar">
  <a href="index.php" class="brand">
    <span class="brand-logo"><i class="bi bi-file-earmark-text-fill"></i></span>
    <span><b>EDMS</b><small>Гардиши ҳуҷҷатҳо</small></span>
  </a>
  <nav>
    <div class="nav-label">Асосӣ</div>
    <?php
    side_link('index.php', 'grid-1x2-fill', 'Саҳифаи асосӣ', $active);
    side_link('documents.php', 'folder2-open', 'Ҳуҷҷатҳо', $active);
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
  <div class="side-user">
    <div class="avatar"><?= e(mb_strtoupper(mb_substr($u['full_name'], 0, 1))) ?></div>
    <div class="meta"><b><?= e($u['full_name']) ?></b><small><?= e(ROLES[$r]) ?></small></div>
    <a href="logout.php" class="icon-btn" style="width:36px;height:36px" title="Баромад"><i class="bi bi-box-arrow-right"></i></a>
  </div>
</aside>

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
