<?php
session_start();
date_default_timezone_set('Asia/Dushanbe');

$pdo = new PDO('mysql:host=localhost;dbname=edms;charset=utf8mb4', 'root', '', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
]);

const ROLES = [
    'chancellery' => 'Девонхона',
    'director'    => 'Роҳбар',
    'dept_head'   => 'Сардори шӯъба',
    'employee'    => 'Корманд',
    'admin'       => 'Администратор',
];
const DOC_TYPES = ['incoming' => 'Воридотӣ', 'outgoing' => 'Содиротӣ', 'internal' => 'Дохилӣ'];
const IMPORTANCE = ['low' => 'Паст', 'normal' => 'Муқаррарӣ', 'high' => 'Муҳим'];
const DOC_STATUS = [
    'registered'  => 'Бақайдгирифта',
    'review'      => 'Дар баррасӣ',
    'in_progress' => 'Дар иҷро',
    'approved'    => 'Тасдиқшуда',
    'rejected'    => 'Радшуда',
    'done'        => 'Иҷрошуда',
];
const TASK_STATUS = ['new' => 'Нав', 'in_progress' => 'Дар иҷро', 'done' => 'Иҷрошуда'];

function e($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
function me() { return $_SESSION['user'] ?? null; }
function require_login() { if (!me()) { header('Location: login.php'); exit; } }
function require_role(array $roles) {
    require_login();
    if (!in_array(me()['role'], $roles, true)) { http_response_code(403); exit('Дастрасӣ манъ аст'); }
}
function csrf() {
    if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(16));
    return $_SESSION['csrf'];
}
function csrf_field() { return '<input type="hidden" name="csrf" value="' . csrf() . '">'; }
function csrf_check() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && !hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf'] ?? '')) {
        http_response_code(400); exit('Хатогии CSRF');
    }
}
function flash($msg = null, $type = 'success') {
    if ($msg !== null) { $_SESSION['flash'] = [$msg, $type]; return; }
    $f = $_SESSION['flash'] ?? null; unset($_SESSION['flash']); return $f;
}
function upload_file($field, array $allowed = ['pdf', 'doc', 'docx', 'jpg', 'jpeg']) {
    if (empty($_FILES[$field]['name']) || $_FILES[$field]['error'] !== UPLOAD_ERR_OK) return null;
    $ext = strtolower(pathinfo($_FILES[$field]['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowed, true)) return null;
    $name = bin2hex(random_bytes(8)) . '.' . $ext;
    move_uploaded_file($_FILES[$field]['tmp_name'], __DIR__ . '/uploads/' . $name);
    return $name;
}
function is_overdue($deadline, $status) {
    return $status !== 'done' && strtotime($deadline) < time();
}

const STATUS_COLOR = [
    'registered' => '#64748b', 'review' => '#f59e0b', 'in_progress' => '#0ea5e9',
    'approved' => '#10b981', 'rejected' => '#ef4444', 'done' => '#8b5cf6',
];
function status_badge($s) {
    return '<span class="badge-soft" style="--c:' . (STATUS_COLOR[$s] ?? '#64748b') . '">' . e(DOC_STATUS[$s] ?? $s) . '</span>';
}
function time_left($deadline) {
    $d = strtotime($deadline) - time();
    $abs = abs($d);
    if ($abs >= 86400) $t = floor($abs / 86400) . ' рӯз';
    elseif ($abs >= 3600) $t = floor($abs / 3600) . ' соат';
    else $t = max(1, floor($abs / 60)) . ' дақиқа';
    return $d < 0 ? "$t пеш гузашт" : "$t монд";
}
