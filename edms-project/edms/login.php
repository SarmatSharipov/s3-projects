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
<html lang="tg" data-bs-theme="light">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Вуруд — EDMS</title>
<script>try{document.documentElement.setAttribute('data-bs-theme',localStorage.getItem('theme')||(matchMedia('(prefers-color-scheme: dark)').matches?'dark':'light'))}catch(e){}</script>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
<link href="assets/style.css?v=2" rel="stylesheet">
</head>
<body>
<div class="auth">
  <section class="auth-hero">
    <span class="shape s1"></span><span class="shape s2"></span><span class="shape s3"></span>
    <div class="brand text-white" style="padding:0">
      <span class="brand-logo" style="background:rgba(255,255,255,.2);box-shadow:none"><i class="bi bi-file-earmark-text-fill"></i></span>
      <span><b style="font-size:1.4rem">EDMS</b></span>
    </div>
    <div>
      <h1>Гардиши электронии ҳуҷҷатҳо — тез, шаффоф, бе коғаз.</h1>
      <p class="mt-3">Бақайдгирӣ, резолютсия, супоришҳо ва мувофиқасозӣ дар як система.</p>
    </div>
    <div class="chips">
      <span class="chip" style="animation-delay:.4s"><i class="bi bi-lightning-charge-fill"></i> Суръати кор</span>
      <span class="chip" style="animation-delay:.55s"><i class="bi bi-shield-check"></i> Амният</span>
      <span class="chip" style="animation-delay:.7s"><i class="bi bi-bell-fill"></i> Назорати мӯҳлат</span>
      <span class="chip" style="animation-delay:.85s"><i class="bi bi-graph-up-arrow"></i> Ҳисоботҳо</span>
    </div>
  </section>

  <section class="auth-form-side">
    <button class="icon-btn auth-theme" data-theme-toggle aria-label="Тема"><i class="bi bi-moon-stars"></i></button>
    <div class="card auth-card <?= $error ? 'shake' : '' ?>">
      <h3 class="mb-1">Хуш омадед 👋</h3>
      <p class="text-muted mb-4">Барои идома ба ҳисоби худ ворид шавед</p>
      <?php if ($error): ?><div class="alert alert-danger py-2"><i class="bi bi-exclamation-circle me-1"></i><?= e($error) ?></div><?php endif; ?>
      <form method="post">
        <?= csrf_field() ?>
        <div class="mb-3">
          <label class="form-label">Логин</label>
          <input name="login" class="form-control" required autofocus autocomplete="username" placeholder="admin">
        </div>
        <div class="mb-4">
          <label class="form-label">Парол</label>
          <div class="pw-wrap">
            <input type="password" name="password" class="form-control" required autocomplete="current-password" placeholder="••••••••">
            <button type="button" data-pw tabindex="-1"><i class="bi bi-eye"></i></button>
          </div>
        </div>
        <button class="btn btn-primary w-100 py-2">Ворид шудан <i class="bi bi-arrow-right ms-1"></i></button>
      </form>
    </div>
  </section>
</div>
<script src="assets/app.js?v=2"></script>
</body>
</html>
