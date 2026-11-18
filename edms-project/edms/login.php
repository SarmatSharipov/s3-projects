<?php
require 'config.php';
if (me()) { header('Location: index.php'); exit; }
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $st = $pdo->prepare('SELECT * FROM users WHERE login = ?');
    $st->execute([trim($_POST['login'] ?? '')]);
    $user = $st->fetch();
    if ($user && password_verify($_POST['password'] ?? '', $user['password'])) {
        session_regenerate_id(true);
        unset($user['password']);
        $_SESSION['user'] = $user;
        header('Location: index.php'); exit;
    }
    $error = 'Логин ё парол нодуруст аст';
}
?>
<!doctype html>
<html lang="tg" data-bs-theme="dark">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Вуруд — EDMS</title>
<script>try{document.documentElement.setAttribute('data-bs-theme',localStorage.getItem('edms-theme')||'dark')}catch(e){}</script>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
<link href="assets/style.css?v=2" rel="stylesheet">
<link href="assets/glass.css?v=8" rel="stylesheet">
</head>
<body class="login-body">
<div class="lg-orb o1"></div><div class="lg-orb o2"></div><div class="lg-orb o3"></div>
<button class="icon-btn auth-theme" data-theme-toggle aria-label="Тема"><i class="bi bi-moon-stars"></i></button>

<main class="lg">
  <section class="lg-intro">
    <div class="lg-brand">
      <span class="brand-logo"><i class="bi bi-file-earmark-text-fill"></i></span>
      <span><b>EDMS</b><small>Гардиши электронии ҳуҷҷатҳо</small></span>
    </div>
    <h1>Ҳуҷҷатҳо — <em>тез, шаффоф, бе коғаз.</em></h1>
    <p>Бақайдгирӣ, мувофиқасозӣ, резолютсия ва назорати мӯҳлат дар як системаи ягона.</p>

    <div class="lg-mock" aria-hidden="true">
      <div class="m-card m1"><span class="dot" style="--c:#f59e0b"></span><b>Дар баррасӣ</b><small>Мувофиқа 2/3</small><i style="width:66%"></i></div>
      <div class="m-card m2"><span class="dot" style="--c:#0ea5e9"></span><b>Дар иҷро</b><small>Мӯҳлат: 2 рӯз монд</small><i style="width:40%"></i></div>
      <div class="m-card m3"><span class="dot" style="--c:#10b981"></span><b>Тасдиқшуда</b><small>Резолютсия гузошта шуд</small><i style="width:100%"></i></div>
    </div>

    <div class="chips">
      <span class="chip"><i class="bi bi-lightning-charge-fill"></i> Суръати кор</span>
      <span class="chip"><i class="bi bi-shield-check"></i> Амният</span>
      <span class="chip"><i class="bi bi-bell-fill"></i> Назорати мӯҳлат</span>
      <span class="chip"><i class="bi bi-graph-up-arrow"></i> Ҳисоботҳо</span>
    </div>
  </section>

  <section class="lg-form">
    <div class="card lg-card <?= $error ? 'shake' : '' ?>">
      <div class="lg-avatar"><i class="bi bi-person-fill"></i></div>
      <h2>Хуш омадед</h2>
      <p class="text-muted">Барои идома ба ҳисоби худ ворид шавед</p>
      <?php if ($error): ?><div class="alert alert-danger py-2"><i class="bi bi-exclamation-circle me-1"></i><?= e($error) ?></div><?php endif; ?>
      <form method="post">
        <?= csrf_field() ?>
        <label class="form-label" for="lg-login">Логин</label>
        <div class="lg-field"><i class="bi bi-person"></i>
          <input id="lg-login" name="login" class="form-control" required autofocus autocomplete="username" autocapitalize="none" spellcheck="false" placeholder="Логин">
        </div>
        <label class="form-label" for="lg-pass">Парол</label>
        <div class="lg-field pw-wrap"><i class="bi bi-lock"></i>
          <input id="lg-pass" type="password" name="password" class="form-control" required autocomplete="current-password" placeholder="••••••••">
          <button type="button" data-pw tabindex="-1" aria-label="Нишон додан"><i class="bi bi-eye"></i></button>
        </div>
        <button class="btn btn-primary lg-btn w-100">Ворид шудан <i class="bi bi-arrow-right ms-1"></i></button>
      </form>
      <div class="lg-foot"><i class="bi bi-shield-lock"></i> Пайвасти бехатар · EDMS 2026</div>
    </div>
  </section>
</main>
<script src="assets/app.js?v=5"></script>
</body>
</html>
