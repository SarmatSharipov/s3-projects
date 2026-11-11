<?php
require 'config.php';
require_role(['admin']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    header('Content-Type: application/sql; charset=utf-8');
    header('Content-Disposition: attachment; filename="edms_backup_' . date('Ymd_His') . '.sql"');
    echo "SET FOREIGN_KEY_CHECKS=0;\nSET NAMES utf8mb4;\n\n";
    foreach ($pdo->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN) as $t) {
        $create = $pdo->query("SHOW CREATE TABLE `$t`")->fetch(PDO::FETCH_NUM)[1];
        echo "DROP TABLE IF EXISTS `$t`;\n$create;\n\n";
        foreach ($pdo->query("SELECT * FROM `$t`") as $row) {
            $vals = array_map(fn($v) => $v === null ? 'NULL' : $pdo->quote($v), array_values($row));
            echo "INSERT INTO `$t` VALUES (" . implode(',', $vals) . ");\n";
        }
        echo "\n";
    }
    echo "SET FOREIGN_KEY_CHECKS=1;\n";
    exit;
}
include 'includes/header.php';
?>
<h4 class="mb-3">Нусхаи эҳтиётии базаи додаҳо</h4>
<div class="card card-body">
  <p>Файли .sql-и пурраи базаи додаҳоро зеркашӣ кунед.</p>
  <form method="post"><?= csrf_field() ?><button class="btn btn-primary">Зеркашии backup</button></form>
</div>
<?php include 'includes/footer.php'; ?>
