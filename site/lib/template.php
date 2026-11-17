<?php
/**
 * Утилиты и части шаблона (шапка/подвал) для публичных страниц.
 */

function appConfig(): array
{
    static $config = null;
    if ($config === null) {
        $config = require __DIR__ . '/config.php';
    }
    return $config;
}

function renderHeader(string $active = ''): void
{
    $cfg = appConfig();
    $name = $cfg['app']['name'];
    $slogan = $cfg['app']['slogan'];
    $adminLink = isAdminLoggedIn()
        ? '<a href="admin/index.php">Панель управления</a>'
        : '<a href="login.php">Вход для администратора</a>';
    ?>
<!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= e($name) ?></title>
<link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
<header class="site-header">
  <div class="container header-inner">
    <a href="/index.php" class="logo"><?= e($name) ?></a>
    <nav class="main-nav">
      <a href="/index.php" class="<?= $active === 'home' ? 'active' : '' ?>">Главная</a>
      <a href="/catalog.php" class="<?= $active === 'catalog' ? 'active' : '' ?>">Каталог</a>
      <?= $adminLink ?>
    </nav>
  </div>
  <div class="slogan"><?= e($slogan) ?></div>
</header>
<main class="container page">
    <?php
}

function fieldError(string $field, array $errors): string
{
    if (empty($errors[$field])) {
        return '';
    }
    return '<div class="error-text">' . e($errors[$field]) . '</div>';
}

function renderFooter(): void
{
    $cfg = appConfig();
    $a = $cfg['app'];
    ?>
</main>
<footer class="site-footer">
  <div class="container footer-inner">
    <div class="contacts">
      <p><strong>Контакты</strong></p>
      <p>Адрес: <?= e($a['contact_address']) ?></p>
      <p>Режим работы: <?= e($a['contact_schedule']) ?></p>
      <p>Телефон: <?= e($a['contact_phone']) ?></p>
      <p>E-mail: <?= e($a['contact_email']) ?></p>
    </div>
  </div>
</footer>
</body>
</html>
    <?php
}