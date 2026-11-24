<?php
/** @var App\Core\View $view */
ob_start();
?>
<div class="text-center py-5">
  <h1 class="display-1"><?= (int)$code ?></h1>
  <h2 class="h4"><?= $view->e($title) ?></h2>
  <p class="text-muted"><?= $view->e($message) ?></p>
  <a class="btn btn-primary" href="/">Ба саҳифаи асосӣ</a>
</div>
<?php
$content = ob_get_clean();
require dirname(__DIR__) . '/layout.php';
