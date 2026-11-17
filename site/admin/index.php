<?php
/**
 * Панель управления сайтом (задание 4).
 * Просмотр заявок, одобрение, удаление.
 */
require_once __DIR__ . '/../lib/db.php';
require_once __DIR__ . '/../lib/functions.php';
require_once __DIR__ . '/../lib/auth.php';
require_once __DIR__ . '/../lib/validation.php';

requireAdmin();

// Обработка действий
$message = '';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrf($_POST['csrf'] ?? '')) {
        $error = 'Недействительный токен безопасности. Повторите попытку.';
    } else {
        $action = $_POST['action'] ?? '';
        $id = (int)($_POST['id'] ?? 0);

        if ($action === 'approve') {
            updateBookingStatus($id, 'Одобрена')
                ? $message = 'Заявка одобрена.'
                : $error = 'Не удалось одобрить заявку.';
        } elseif ($action === 'reject') {
            updateBookingStatus($id, 'Отклонена')
                ? $message = 'Заявка отклонена.'
                : $error = 'Не удалось отклонить заявку.';
        } elseif ($action === 'delete') {
            deleteBooking($id)
                ? $message = 'Заявка удалена.'
                : $error = 'Не удалось удалить заявку.';
        }
    }
    if ($message || $error) {
        header('Location: index.php?message=' . urlencode($message) . '&error=' . urlencode($error));
        exit;
    }
}

$message = $_GET['message'] ?? '';
$error = $_GET['error'] ?? '';
$bookings = getAllBookings();

require_once __DIR__ . '/../lib/template.php';
renderHeader('admin');
?>
<h1>Панель управления</h1>

<?php if ($message): ?>
  <div class="success-message"><?= e($message) ?></div>
<?php endif; ?>
<?php if ($error): ?>
  <div class="alert alert-error"><?= e($error) ?></div>
<?php endif; ?>

<div class="admin-actions">
  <a href="index.php" class="btn btn-secondary">Обновить</a>
  <a href="../logout.php" class="btn btn-danger">Выход</a>
</div>

<?php if (empty($bookings)): ?>
  <p class="empty-state">Заявок на бронирование пока нет.</p>
<?php else: ?>
  <table class="admin-table">
    <thead>
      <tr>
        <th>ID</th>
        <th>Номер</th>
        <th>Гость</th>
        <th>Телефон / E-mail</th>
        <th>Период</th>
        <th>Статус</th>
        <th>Действия</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($bookings as $b): ?>
        <tr>
          <td><?= (int)$b['id'] ?></td>
          <td><?= e($b['category']) ?><br><small><?= number_format((float)$b['price'], 0, ',', ' ') ?> ₽/сут</small></td>
          <td><?= e($b['last_name']) ?> <?= e($b['first_name']) ?></td>
          <td><?= e($b['phone']) ?><br><small><?= e($b['email']) ?></small></td>
          <td><?= e(date('d.m.Y', strtotime($b['date_in']))) ?> — <?= e(date('d.m.Y', strtotime($b['date_out']))) ?></td>
          <td>
            <span class="status status-<?= e(strtolower($b['status'])) ?>"><?= e($b['status']) ?></span>
          </td>
          <td class="actions">
            <?php if ($b['status'] !== 'Одобрена'): ?>
              <form method="post" class="inline-form">
                <input type="hidden" name="csrf" value="<?= e(csrfToken()) ?>">
                <input type="hidden" name="id" value="<?= (int)$b['id'] ?>">
                <input type="hidden" name="action" value="approve">
                <button type="submit" class="btn btn-success btn-sm">Одобрить</button>
              </form>
            <?php endif; ?>
            <?php if ($b['status'] !== 'Отклонена'): ?>
              <form method="post" class="inline-form">
                <input type="hidden" name="csrf" value="<?= e(csrfToken()) ?>">
                <input type="hidden" name="id" value="<?= (int)$b['id'] ?>">
                <input type="hidden" name="action" value="reject">
                <button type="submit" class="btn btn-secondary btn-sm">Отклонить</button>
              </form>
            <?php endif; ?>
            <form method="post" class="inline-form" onsubmit="return confirm('Удалить заявку?');">
              <input type="hidden" name="csrf" value="<?= e(csrfToken()) ?>">
              <input type="hidden" name="id" value="<?= (int)$b['id'] ?>">
              <input type="hidden" name="action" value="delete">
              <button type="submit" class="btn btn-danger btn-sm">Удалить</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
<?php endif; ?>
<?php renderFooter(); ?>