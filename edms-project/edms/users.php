<?php
require 'config.php';
require_role(['admin']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $a = $_POST['action'] ?? '';
    try {
        if ($a === 'add_user' && isset(ROLES[$_POST['role']]) && strlen($_POST['password']) >= 6) {
            $pdo->prepare('INSERT INTO users (full_name, login, password, role, department_id) VALUES (?,?,?,?,?)')
                ->execute([trim($_POST['full_name']), trim($_POST['login']), password_hash($_POST['password'], PASSWORD_DEFAULT),
                           $_POST['role'], $_POST['department_id'] ?: null]);
            flash('Корбар илова шуд');
        } elseif ($a === 'delete_user' && (int)$_POST['id'] !== me()['id']) {
            $pdo->prepare('DELETE FROM users WHERE id=?')->execute([(int)$_POST['id']]);
            flash('Корбар нест карда шуд');
        } elseif ($a === 'reset_pass' && strlen($_POST['password']) >= 6) {
            $pdo->prepare('UPDATE users SET password=? WHERE id=?')
                ->execute([password_hash($_POST['password'], PASSWORD_DEFAULT), (int)$_POST['id']]);
            flash('Парол иваз шуд');
        } elseif ($a === 'add_dept' && trim($_POST['name']) !== '') {
            $pdo->prepare('INSERT INTO departments (name) VALUES (?)')->execute([trim($_POST['name'])]);
            flash('Шӯъба илова шуд');
        } elseif ($a === 'delete_dept') {
            $pdo->prepare('DELETE FROM departments WHERE id=?')->execute([(int)$_POST['id']]);
            flash('Шӯъба нест карда шуд');
        } else { flash('Маълумот нодуруст (парол ≥ 6 ҳарф)', 'danger'); }
    } catch (PDOException $e) {
        flash('Хатогӣ: корбар ба ҳуҷҷатҳо/супоришҳо пайваст аст ё логин такрорист', 'danger');
    }
    header('Location: users.php'); exit;
}

$depts = $pdo->query('SELECT * FROM departments ORDER BY name')->fetchAll();
$users = $pdo->query('SELECT u.*, d.name dept FROM users u LEFT JOIN departments d ON d.id=u.department_id ORDER BY u.id')->fetchAll();
include 'includes/header.php';
?>
<h4 class="mb-3">Идоракунии корбарон</h4>
<form method="post" class="card card-body mb-4"><?= csrf_field() ?><input type="hidden" name="action" value="add_user">
  <div class="row g-2">
    <div class="col-md-3"><input name="full_name" class="form-control" placeholder="Ному насаб" required></div>
    <div class="col-md-2"><input name="login" class="form-control" placeholder="Логин" required></div>
    <div class="col-md-2"><input type="password" name="password" class="form-control" placeholder="Парол" required></div>
    <div class="col-md-2"><select name="role" class="form-select"><?php foreach (ROLES as $k => $v): ?><option value="<?= $k ?>"><?= $v ?></option><?php endforeach; ?></select></div>
    <div class="col-md-2"><select name="department_id" class="form-select"><option value="">Бе шӯъба</option>
      <?php foreach ($depts as $d): ?><option value="<?= $d['id'] ?>"><?= e($d['name']) ?></option><?php endforeach; ?></select></div>
    <div class="col-md-1"><button class="btn btn-primary w-100">+</button></div>
  </div>
</form>
<div class="card mb-4"><div class="table-responsive"><table class="table align-middle mb-0">
  <tr><th>Ном</th><th>Логин</th><th>Нақш</th><th>Шӯъба</th><th></th></tr>
  <?php foreach ($users as $u): ?>
  <tr><td><?= e($u['full_name']) ?></td><td><?= e($u['login']) ?></td><td><?= e(ROLES[$u['role']]) ?></td><td><?= e($u['dept']) ?></td>
    <td class="d-flex gap-1">
      <form method="post" class="d-flex gap-1"><?= csrf_field() ?><input type="hidden" name="action" value="reset_pass"><input type="hidden" name="id" value="<?= $u['id'] ?>">
        <input type="password" name="password" class="form-control form-control-sm" placeholder="Парол нав" style="width:130px">
        <button class="btn btn-sm btn-outline-secondary">Иваз</button></form>
      <?php if ($u['id'] != me()['id']): ?>
      <form method="post" onsubmit="return confirm('Нест кунем?')"><?= csrf_field() ?><input type="hidden" name="action" value="delete_user"><input type="hidden" name="id" value="<?= $u['id'] ?>">
        <button class="btn btn-sm btn-outline-danger">Нест</button></form>
      <?php endif; ?>
    </td></tr>
  <?php endforeach; ?>
</table></div></div>

<h5>Шӯъбаҳо</h5>
<div class="card"><div class="card-body">
  <?php foreach ($depts as $d): ?>
  <form method="post" class="d-flex justify-content-between border-bottom py-1" onsubmit="return confirm('Нест кунем?')"><?= csrf_field() ?>
    <input type="hidden" name="action" value="delete_dept"><input type="hidden" name="id" value="<?= $d['id'] ?>">
    <span><?= e($d['name']) ?></span><button class="btn btn-sm btn-outline-danger">Нест</button></form>
  <?php endforeach; ?>
  <form method="post" class="d-flex gap-2 mt-3"><?= csrf_field() ?><input type="hidden" name="action" value="add_dept">
    <input name="name" class="form-control" placeholder="Номи шӯъбаи нав"><button class="btn btn-primary">Илова</button></form>
</div></div>
<?php include 'includes/footer.php'; ?>
