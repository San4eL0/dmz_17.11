<?php
require_once __DIR__ . '/lib/db.php';
require_once __DIR__ . '/lib/functions.php';
require_once __DIR__ . '/lib/auth.php';
require_once __DIR__ . '/lib/validation.php';
require_once __DIR__ . '/lib/template.php';

session_start_safe();

// Уже вошёл — в панель
if (isAdminLoggedIn()) {
    header('Location: /admin/index.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrf($_POST['csrf'] ?? '')) {
        $error = 'Недействительный токен безопасности. Обновите страницу и повторите попытку.';
    } else {
        $login = trim($_POST['login'] ?? '');
        $password = $_POST['password'] ?? '';
        if (adminLogin($login, $password)) {
            header('Location: /admin/index.php');
            exit;
        }
        $error = 'Неверный логин или пароль.';
    }
}

renderHeader();
?>
<div class="auth-wrap">
  <h1>Вход для администратора</h1>

  <?php if ($error): ?>
    <div class="alert alert-error"><?= e($error) ?></div>
  <?php endif; ?>

  <form class="auth-form" method="post" action="/login.php">
    <input type="hidden" name="csrf" value="<?= e(csrfToken()) ?>">

    <div class="form-field">
      <label for="login">Логин</label>
      <input type="text" id="login" name="login" value="<?= e($_POST['login'] ?? '') ?>" required>
    </div>

    <div class="form-field">
      <label for="password">Пароль</label>
      <input type="password" id="password" name="password" required>
    </div>

    <button type="submit" class="btn btn-primary">Войти</button>
  </form>
</div>
<?php renderFooter(); ?>