<?php
require_once __DIR__ . '/lib/db.php';
require_once __DIR__ . '/lib/functions.php';
require_once __DIR__ . '/lib/auth.php';
require_once __DIR__ . '/lib/validation.php';
require_once __DIR__ . '/lib/template.php';

session_start_safe();

// Фильтрация на стороне сервера
$filter = [
    'category' => isset($_GET['category']) ? trim($_GET['category']) : '',
    'price_min' => isset($_GET['price_min']) ? trim($_GET['price_min']) : '',
    'price_max' => isset($_GET['price_max']) ? trim($_GET['price_max']) : '',
    'places' => isset($_GET['places']) ? trim($_GET['places']) : '',
];

$categories = getCategories();
$rooms = getRooms($filter);
$count = count($rooms);

renderHeader('catalog');
?>
<h1>Каталог номеров</h1>
<p class="result-count">Найдено номеров: <?= (int)$count ?> из 6.</p>

<form class="filter-form" method="get" action="/catalog.php">
  <div class="filter-group">
    <label for="category">Категория</label>
    <select id="category" name="category">
      <option value="">Все категории</option>
      <?php foreach ($categories as $cat): ?>
        <option value="<?= e($cat) ?>" <?= $filter['category'] === $cat ? 'selected' : '' ?>>
          <?= e($cat) ?>
        </option>
      <?php endforeach; ?>
    </select>
  </div>

  <div class="filter-group">
    <label for="price_min">Цена от (₽)</label>
    <input type="number" id="price_min" name="price_min" min="0"
           value="<?= e($filter['price_min']) ?>" placeholder="0">
  </div>

  <div class="filter-group">
    <label for="price_max">Цена до (₽)</label>
    <input type="number" id="price_max" name="price_max" min="0"
           value="<?= e($filter['price_max']) ?>" placeholder="∞">
  </div>

  <div class="filter-group">
    <label for="places">Количество мест</label>
    <select id="places" name="places">
      <option value="">Любое</option>
      <?php for ($p = 1; $p <= 5; $p++): ?>
        <option value="<?= $p ?>" <?= (string)$filter['places'] === (string)$p ? 'selected' : '' ?>>
          <?= $p ?> <?= $p === 1 ? 'место' : 'места' ?>
        </option>
      <?php endfor; ?>
    </select>
  </div>

  <div class="filter-actions">
    <button type="submit" class="btn btn-primary">Применить</button>
    <a href="/catalog.php" class="btn btn-secondary">Сбросить фильтр</a>
  </div>
</form>

<?php if (empty($rooms)): ?>
  <p class="empty-state">По заданным условиям номера не найдены.</p>
<?php else: ?>
  <div class="room-grid">
    <?php foreach ($rooms as $room): ?>
      <article class="room-card">
        <?= imageTag($room) ?>
        <h2><?= e($room['category']) ?></h2>
        <p class="price"><?= number_format((float)$room['price'], 0, ',', ' ') ?> ₽ / сутки</p>
        <ul class="features">
          <li><?= (int)$room['places'] ?> <?= (int)$room['places'] === 1 ? 'место' : 'места' ?></li>
          <li><?= rtrim(rtrim(number_format((float)$room['area'], 2, '.', ''), '0'), '.') ?> м²</li>
          <?php if ($room['has_conditioner']): ?><li>Кондиционер</li><?php endif; ?>
          <?php if ($room['has_tv']): ?><li>Телевизор</li><?php endif; ?>
          <?php if ($room['has_fridge']): ?><li>Холодильник</li><?php endif; ?>
          <?php if ($room['has_wifi']): ?><li>Wi-Fi</li><?php endif; ?>
        </ul>
        <a href="/booking.php?id=<?= (int)$room['id'] ?>" class="btn btn-primary">Забронировать</a>
      </article>
    <?php endforeach; ?>
  </div>
<?php endif; ?>
<?php renderFooter(); ?>