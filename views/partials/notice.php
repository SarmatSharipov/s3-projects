<?php
$messages = [
    'created' => 'Ҳуҷҷат бақайд гирифта шуд',
    'updated' => 'Ҳуҷҷат навсозӣ шуд',
    'deleted' => 'Ҳуҷҷат нест карда шуд',
];
if (!empty($msg) && isset($messages[$msg])):
?>
<div class="alert alert-success alert-dismissible fade show" role="alert">
  <?= $view->e($messages[$msg]) ?>
  <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>
