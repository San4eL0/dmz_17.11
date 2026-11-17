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
  <div class="term-meta"><span class="blink">●</span> ИНФО-СИСТЕМА // БРОНИРОВАНИЕ НОМЕРОВ</div>
  <h1 class="glitch" data-text="Комфорт будущего, отдых в реальности">
    Комфорт будущего,<br><span class="accent-line">отдых в реальности</span>
  </h1>
  <p>Современные номера, интеллектуальная система бронирования и забота о каждом госте.
     Выберите категорию, задайте нужные параметры и оформите заявку за пару кликов.</p>
  <a href="catalog.php" class="btn btn-primary">Перейти в каталог</a>
</section>

<section class="feature-grid">
  <div class="feature-card">
    <h3>Каталог и фильтры</h3>
    <p>Поиск номера по категории, цене и количеству мест с серверной фильтрацией.</p>
  </div>
  <div class="feature-card">
    <h3>Онлайн-бронирование</h3>
    <p>Заявка за пару минут: даты заезда и выезда, контакты — и подтверждение.</p>
  </div>
  <div class="feature-card">
    <h3>Панель администратора</h3>
    <p>Модерация заявок: одобрение, отклонение и удаление по одному клику.</p>
  </div>
</section>
<?php renderFooter(); ?>