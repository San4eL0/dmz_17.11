<?php
/**
 * Работа с каталогом номеров и заявками на бронирование
 */

/**
 * Список номеров с фильтрацией (серверная сторона).
 * $filter: category, price_min, price_max, places
 */
function getRooms(array $filter = []): array
{
    $sql = 'SELECT * FROM rooms WHERE 1=1';
    $params = [];

    if (!empty($filter['category'])) {
        $sql .= ' AND category = :category';
        $params['category'] = $filter['category'];
    }
    if (($filter['price_min'] ?? '') !== '') {
        $sql .= ' AND price >= :price_min';
        $params['price_min'] = (float)$filter['price_min'];
    }
    if (($filter['price_max'] ?? '') !== '') {
        $sql .= ' AND price <= :price_max';
        $params['price_max'] = (float)$filter['price_max'];
    }
    if (!empty($filter['places'])) {
        $sql .= ' AND places = :places';
        $params['places'] = (int)$filter['places'];
    }

    $sql .= ' ORDER BY id';

    $stmt = db()->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

/**
 * Получить один номер по id.
 */
function getRoom(int $id): ?array
{
    $stmt = db()->prepare('SELECT * FROM rooms WHERE id = :id');
    $stmt->execute(['id' => $id]);
    $room = $stmt->fetch();
    return $room ?: null;
}

/**
 * Список категорий номеров (для фильтра).
 */
function getCategories(): array
{
    $stmt = db()->query('SELECT DISTINCT category FROM rooms ORDER BY category');
    return $stmt->fetchAll(PDO::FETCH_COLUMN);
}

/**
 * Создать заявку на бронирование.
 */
function createBooking(array $data): int
{
    $stmt = db()->prepare(
        'INSERT INTO bookings (room_id, first_name, last_name, phone, email, date_in, date_out)
         VALUES (:room_id, :first_name, :last_name, :phone, :email, :date_in, :date_out)'
    );
    $stmt->execute([
        'room_id' => $data['room_id'],
        'first_name' => $data['first_name'],
        'last_name' => $data['last_name'],
        'phone' => $data['phone'],
        'email' => $data['email'],
        'date_in' => $data['date_in'],
        'date_out' => $data['date_out'],
    ]);
    return (int)db()->lastInsertId();
}

/**
 * Список всех заявок (для панели администратора).
 */
function getAllBookings(): array
{
    $stmt = db()->query(
        'SELECT b.*, r.category, r.price
         FROM bookings b
         JOIN rooms r ON r.id = b.room_id
         ORDER BY b.created_at DESC'
    );
    return $stmt->fetchAll();
}

/**
 * Обновить статус заявки.
 */
function updateBookingStatus(int $id, string $status): bool
{
    $allowed = ['Новая', 'Одобрена', 'Отклонена'];
    if (!in_array($status, $allowed, true)) {
        return false;
    }
    $stmt = db()->prepare('UPDATE bookings SET status = :status WHERE id = :id');
    return $stmt->execute(['status' => $status, 'id' => $id]);
}

/**
 * Удалить заявку.
 */
function deleteBooking(int $id): bool
{
    $stmt = db()->prepare('DELETE FROM bookings WHERE id = :id');
    return $stmt->execute(['id' => $id]);
}

/**
 * Проверить занятость номера на период [date_in, date_out)
 * наличием "Одобренных" заявок, пересекающихся с периодом.
 */
function hasApprovedBooking(int $roomId, string $dateIn, string $dateOut): bool
{
    $stmt = db()->prepare(
        'SELECT COUNT(*) FROM bookings
         WHERE room_id = :room_id
           AND status = :status
           AND date_in < :date_out
           AND date_out > :date_in'
    );
    $stmt->execute([
        'room_id' => $roomId,
        'status' => 'Одобрена',
        'date_in' => $dateIn,
        'date_out' => $dateOut,
    ]);
    return (int)$stmt->fetchColumn() > 0;
}

/**
 * Диапазоны "одобренных" броней для номера (для календаря).
 */
function getUnavailableRanges(int $roomId): array
{
    $stmt = db()->prepare(
        'SELECT date_in, date_out FROM bookings
         WHERE room_id = :room_id AND status = :status
         ORDER BY date_in'
    );
    $stmt->execute(['room_id' => $roomId, 'status' => 'Одобрена']);
    return $stmt->fetchAll();
}

/**
 * HTML-тег изображения номера (заглушка, если файла нет).
 */
function imageTag(array $room): string
{
    $src = !empty($room['image']) ? e($room['image']) : '/assets/img/room-default.jpg';
    $alt = e(strip_tags($room['category'] ?? 'Номер'));
    return '<img src="' . $src . '" alt="' . $alt . '">';
}