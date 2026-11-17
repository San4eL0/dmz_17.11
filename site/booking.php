<?php
require_once __DIR__ . '/lib/db.php';
require_once __DIR__ . '/lib/functions.php';
require_once __DIR__ . '/lib/auth.php';
require_once __DIR__ . '/lib/validation.php';
require_once __DIR__ . '/lib/template.php';

session_start_safe();

$room = null;
$roomId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$room = getRoom($roomId);

$errors = [];
$old = [
    'first_name' => '', 'last_name' => '', 'phone' => '',
    'email' => '', 'date_in' => '', 'date_out' => '',
];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $old = array_merge($old, array_map('trim', $_POST));
    $old['room_id'] = (int)($_POST['room_id'] ?? 0);

    $errors = validateBookingInput($old);

    if (empty($errors) && !verifyCsrf($_POST['csrf'] ?? '')) {
        $errors['form'] = 'Недействительный токен безопасности. Обновите страницу и повторите попытку.';
    }

    if (empty($errors)) {
        $roomForBooking = getRoom((int)$old['room_id']);
        // Конфликт с уже одобренной бронью на этот номер
        if ($roomForBooking && hasApprovedBooking(
            (int)$old['room_id'],
            DateTime::createFromFormat('d.m.Y', $old['date_in'])->format('Y-m-d'),
            DateTime::createFromFormat('d.m.Y', $old['date_out'])->format('Y-m-d')
        )) {
            $errors['form'] = 'Номер уже забронирован на выбранный период (есть одобренная бронь). Выберите другие даты.';
        }
    }

    if (empty($errors)) {
        createBooking([
            'room_id' => (int)$old['room_id'],
            'first_name' => $old['first_name'],
            'last_name' => $old['last_name'],
            'phone' => $old['phone'],
            'email' => $old['email'],
            'date_in' => DateTime::createFromFormat('d.m.Y', $old['date_in'])->format('Y-m-d'),
            'date_out' => DateTime::createFromFormat('d.m.Y', $old['date_out'])->format('Y-m-d'),
        ]);
        $success = true;
    }
}

renderHeader();
$title = $room ? 'Бронирование номера' : 'Номер не найден';
?>
<?php if (!$room): ?>
  <h1>Номер не найден</h1>
  <p><a href="/catalog.php" class="btn btn-primary">Вернуться в каталог</a></p>
<?php else: ?>
  <h1>Бронирование номера</h1>
  <p class="room-summary">
    <?= e($room['category']) ?> · <?= (int)$room['places'] ?> мест ·
    <?= rtrim(rtrim(number_format((float)$room['area'], 2, '.', ''), '0'), '.') ?> м² ·
    <?= number_format((float)$room['price'], 0, ',', ' ') ?> ₽/сутки
  </p>

  <?php if (!empty($errors['form'])): ?>
    <div class="alert alert-error"><?= e($errors['form']) ?></div>
  <?php endif; ?>

  <?php if ($success): ?>
    <div class="success-message" id="success">
      Заявка успешно отправлена! Администратор свяжется с вами для подтверждения.
    </div>
  <?php endif; ?>

  <form class="booking-form" method="post" action="/booking.php?id=<?= (int)$room['id'] ?>" novalidate>
    <input type="hidden" name="csrf" value="<?= e(csrfToken()) ?>">
    <input type="hidden" name="room_id" value="<?= (int)$room['id'] ?>">

    <div class="form-field">
      <label for="last_name">Фамилия *</label>
      <input type="text" id="last_name" name="last_name"
             value="<?= e($old['last_name']) ?>" placeholder="Иванов">
      <?= fieldError('last_name', $errors) ?>
    </div>

    <div class="form-field">
      <label for="first_name">Имя *</label>
      <input type="text" id="first_name" name="first_name"
             value="<?= e($old['first_name']) ?>" placeholder="Иван">
      <?= fieldError('first_name', $errors) ?>
    </div>

    <div class="form-field">
      <label for="phone">Телефон *</label>
      <input type="tel" id="phone" name="phone"
             value="<?= e($old['phone']) ?>" placeholder="+7(XXX)XXX-XX-XX">
      <?= fieldError('phone', $errors) ?>
    </div>

    <div class="form-field">
      <label for="email">E-mail *</label>
      <input type="email" id="email" name="email"
             value="<?= e($old['email']) ?>" placeholder="ivan@example.ru">
      <?= fieldError('email', $errors) ?>
    </div>

    <div class="form-field">
      <label for="date_in">Дата заезда *</label>
      <input type="text" id="date_in" name="date_in" placeholder="ДД.ММ.ГГГГ"
             value="<?= e($old['date_in']) ?>" autocomplete="off">
      <?= fieldError('date_in', $errors) ?>
    </div>

    <div class="form-field">
      <label for="date_out">Дата выезда *</label>
      <input type="text" id="date_out" name="date_out" placeholder="ДД.ММ.ГГГГ"
             value="<?= e($old['date_out']) ?>" autocomplete="off">
      <?= fieldError('date_out', $errors) ?>
    </div>

    <p class="hint">Периоды с одобренной бронёй этого номера в календаре недоступны.</p>

    <button type="submit" class="btn btn-primary">Отправить заявку</button>
  </form>

  <script>
    window.BOOKED_RANGES = <?= json_encode(getUnavailableRanges((int)$room['id']), JSON_UNESCAPED_UNICODE) ?>;
  </script>
  <script src="/assets/js/booking.js"></script>
<?php endif; ?>
<?php renderFooter(); ?>