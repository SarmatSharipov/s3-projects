<?php
/** @var App\Core\View $view */
$typeLabels = ['incoming' => 'Воридотӣ', 'outgoing' => 'Содиротӣ', 'internal' => 'Дохилӣ'];
ob_start();
?>
<h1 class="h3 mb-3">Ҳисоботи ҳуҷҷатҳо аз рӯи намуд</h1>
<div class="panel p-0">
  <div class="table-responsive">
    <table class="table table-hover mb-0">
      <thead>
        <tr>
          <th>Намуд</th>
          <th class="text-end">Ҳамагӣ</th>
          <th class="text-end">Бақайд</th>
          <th class="text-end">Баррасӣ</th>
          <th class="text-end">Иҷро</th>
          <th class="text-end">Тасдиқ</th>
          <th class="text-end">Рад</th>
          <th class="text-end">Иҷрошуда</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($rows as $r): ?>
        <tr>
          <td><?= $typeLabels[$r['type']] ?? $r['type'] ?></td>
          <td class="text-end"><?= (int)$r['total'] ?></td>
          <td class="text-end"><?= (int)$r['registered'] ?></td>
          <td class="text-end"><?= (int)$r['review'] ?></td>
          <td class="text-end"><?= (int)$r['in_progress'] ?></td>
          <td class="text-end"><?= (int)$r['approved'] ?></td>
          <td class="text-end"><?= (int)$r['rejected'] ?></td>
          <td class="text-end"><?= (int)$r['done'] ?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
      <tfoot>
        <tr class="fw-bold">
          <td>Ҷамъ</td>
          <td class="text-end"><?= (int)$totals['total'] ?></td>
          <td class="text-end"><?= (int)$totals['registered'] ?></td>
          <td class="text-end"><?= (int)$totals['review'] ?></td>
          <td class="text-end"><?= (int)$totals['in_progress'] ?></td>
          <td class="text-end"><?= (int)$totals['approved'] ?></td>
          <td class="text-end"><?= (int)$totals['rejected'] ?></td>
          <td class="text-end"><?= (int)$totals['done'] ?></td>
        </tr>
      </tfoot>
    </table>
  </div>
</div>
<?php
$content = ob_get_clean();
require dirname(__DIR__) . '/layout.php';
