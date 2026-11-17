<?php
require_once __DIR__ . '/lib/db.php';
require_once __DIR__ . '/lib/functions.php';
require_once __DIR__ . '/lib/auth.php';
require_once __DIR__ . '/lib/validation.php';
require_once __DIR__ . '/lib/template.php';

session_start_safe();
renderHeader('home');
?>
<section class="hero">
  <h1>Добро пожаловать в гостиницу «Сладкие сны»</h1>
  <p>Комфортные номера, уютная атмосфера и забота о каждом госте.
     Забронируйте номер и наслаждайтесь пребыванием.</p>
  <a href="/catalog.php" class="btn btn-primary">Перейти к каталогу</a>
</section>
<?php renderFooter(); ?>