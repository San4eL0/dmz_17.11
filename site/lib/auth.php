<?php
/**
 * Аутентификация администратора (сессии).
 * Логин по умолчанию: hotel123 / adminHotel
 */

function session_start_safe(): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

/**
 * Проверка логина/пароля администратора.
 */
function adminLogin(string $login, string $password): bool
{
    // Логин по заданию — фиксированный, но проверяем по БД для единообразия.
    $stmt = db()->prepare('SELECT id, password_hash FROM admins WHERE login = :login LIMIT 1');
    $stmt->execute(['login' => $login]);
    $admin = $stmt->fetch();

    if ($admin && password_verify($password, $admin['password_hash'])) {
        session_start_safe();
        session_regenerate_id(true);
        $_SESSION['admin_id'] = (int)$admin['id'];
        $_SESSION['admin_login'] = $login;
        return true;
    }
    return false;
}

/**
 * Вход администратора обязателен для панели управления.
 */
function requireAdmin(): void
{
    session_start_safe();
    if (empty($_SESSION['admin_id'])) {
        header('Location: login.php');
        exit;
    }
}

/**
 * Выход администратора.
 */
function adminLogout(): void
{
    session_start_safe();
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params['path'], $params['domain'],
            $params['secure'], $params['httponly']);
    }
    session_destroy();
}

/**
 * Текущий администратор вошёл в систему?
 */
function isAdminLoggedIn(): bool
{
    session_start_safe();
    return !empty($_SESSION['admin_id']);
}