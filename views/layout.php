<?php
/** @var App\Core\View $view */
/** @var string $title */
/** @var string $content */
?>
<!DOCTYPE html>
<html lang="tg">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= $view->e($title) ?> — EDMS</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="/assets/css/app.css">
</head>
<body>
  <a class="skip-link" href="#main">Гузариш ба мундариҷа</a>
  <header>
    <nav class="navbar navbar-expand-lg app-nav" aria-label="Меню асосӣ">
      <div class="container">
        <a class="navbar-brand fw-bold" href="/">EDMS</a>
        <div class="navbar-nav ms-auto">
          <a class="nav-link" href="/">Асосӣ</a>
          <a class="nav-link" href="/documents">Ҳуҷҷатҳо</a>
          <a class="nav-link" href="/documents/create">Бақайдгирӣ</a>
          <a class="nav-link" href="/reports/status">Ҳисобот</a>
        </div>
      </div>
    </nav>
  </header>
  <main id="main" class="container py-4">
    <?= $content ?>
  </main>
  <footer class="app-footer py-3">
    <div class="container">© 2026 EDMS — Низоми электронии ҳуҷҷатгардиш</div>
  </footer>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
