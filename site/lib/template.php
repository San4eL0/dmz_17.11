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

    $theme = 'theme-neon';
    if (isset($_COOKIE['theme']) && $_COOKIE['theme'] === 'light') {
        $theme = 'theme-light';
    }
    ?>
<!DOCTYPE html>
<html lang="ru" class="<?= $theme ?>">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= e($name) ?></title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Oswald:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<header class="site-header">
  <div class="container header-inner">
    <a href="index.php" class="logo"><?= e($name) ?></a>
    <nav class="main-nav">
      <a href="index.php" class="<?= $active === 'home' ? 'active' : '' ?>">Главная</a>
      <a href="catalog.php" class="<?= $active === 'catalog' ? 'active' : '' ?>">Каталог</a>
      <?= $adminLink ?>
      <button type="button" class="theme-toggle" id="themeToggle" title="Сменить тему" aria-label="Сменить тему">
        <span class="icon-moon">&#9681;</span>
        <span class="icon-sun" hidden>&#9728;</span>
      </button>
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
<script src="assets/js/theme.js"></script>
</body>
</html>
    <?php
}