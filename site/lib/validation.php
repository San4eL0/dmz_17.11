<?php
/**
 * Серверная валидация и базовая безопасность данных
 */

/**
 * Валидация данных формы бронирования (вся валидация — на сервере).
 * Возвращает массив ошибок вида [поле => сообщение].
 */
function validateBookingInput(array $input): array
{
    $errors = [];

    $first_name = trim($input['first_name'] ?? '');
    $last_name = trim($input['last_name'] ?? '');
    $phone = trim($input['phone'] ?? '');
    $email = trim($input['email'] ?? '');
    $date_in = trim($input['date_in'] ?? '');
    $date_out = trim($input['date_out'] ?? '');
    $room_id = (int)($input['room_id'] ?? 0);

    // Имя / фамилия: только кириллица, пробелы, точки и тире
    if ($first_name === '' || !preg_match('/^[А-Яа-яЁё\s.\-]+$/u', $first_name)) {
        $errors['first_name'] = 'Имя обязательно: допускаются кириллица, пробелы, точки и тире.';
    } elseif (mb_strlen($first_name) > 100) {
        $errors['first_name'] = 'Имя не должно превышать 100 символов.';
    }

    if ($last_name === '' || !preg_match('/^[А-Яа-яЁё\s.\-]+$/u', $last_name)) {
        $errors['last_name'] = 'Фамилия обязательна: допускаются кириллица, пробелы, точки и тире.';
    } elseif (mb_strlen($last_name) > 100) {
        $errors['last_name'] = 'Фамилия не должна превышать 100 символов.';
    }

    // Телефон: формат +7(XXX)XXX-XX-XX
    if ($phone === '' || !preg_match('/^\+7\(\d{3}\)\d{3}-\d{2}-\d{2}$/', $phone)) {
        $errors['phone'] = 'Телефон должен быть в формате +7(XXX)XXX-XX-XX.';
    }

    // E-mail
    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Введите корректный e-mail.';
    } elseif (mb_strlen($email) > 150) {
        $errors['email'] = 'E-mail не должен превышать 150 символов.';
    }

    // Даты: формат ДД.ММ.ГГГГ
    if ($date_in === '' || !preg_match('/^\d{2}\.\d{2}\.\d{4}$/', $date_in)) {
        $errors['date_in'] = 'Дата заезда обязательна (формат ДД.ММ.ГГГГ).';
    } else {
        $d = DateTime::createFromFormat('d.m.Y', $date_in);
        $errors_num = DateTime::getLastErrors();
        if (!$d || ($errors_num['warning_count'] + $errors_num['error_count']) > 0) {
            $errors['date_in'] = 'Некорректная дата заезда.';
        }
    }

    if ($date_out === '' || !preg_match('/^\d{2}\.\d{2}\.\d{4}$/', $date_out)) {
        $errors['date_out'] = 'Дата выезда обязательна (формат ДД.ММ.ГГГГ).';
    } else {
        $d = DateTime::createFromFormat('d.m.Y', $date_out);
        $errors_num = DateTime::getLastErrors();
        if (!$d || ($errors_num['warning_count'] + $errors_num['error_count']) > 0) {
            $errors['date_out'] = 'Некорректная дата выезда.';
        }
    }

    if (empty($errors['date_in']) && empty($errors['date_out'])) {
        $in = DateTime::createFromFormat('d.m.Y', $date_in);
        $out = DateTime::createFromFormat('d.m.Y', $date_out);
        $today = new DateTime(date('d.m.Y'));

        if ($in < $today) {
            $errors['date_in'] = 'Дата заезда не может быть раньше текущей даты.';
        }
        if ($out <= $in) {
            $errors['date_out'] = 'Дата выезда должна быть позже даты заезда.';
        }
    }

    if ($room_id <= 0 || getRoom($room_id) === null) {
        $errors['room_id'] = 'Некорректный номер.';
    }

    return $errors;
}

/**
 * Проверка CSRF-токена.
 */
function verifyCsrf(string $token): bool
{
    return isset($_SESSION['csrf']) && hash_equals($_SESSION['csrf'], $token);
}

/**
 * Генерация CSRF-токена.
 */
function csrfToken(): string
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

/**
 * Экранирование вывода (базовая защита от XSS).
 */
function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}