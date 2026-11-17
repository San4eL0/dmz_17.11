<?php
/**
 * Конфигурация сервиса «Сладкие сны»
 *
 * Настройки БД берутся из переменных окружения (InfinityFree задаёт их
 * автоматически через файл /home/USER/htdocs/dbconfig.php, но мы можем
 * указать их и вручную ниже). Для локального запуска установите значения
 * по умолчанию.
 */
$dbHost = getenv('DB_HOST') ?: '127.0.0.1';
$dbPort = getenv('DB_PORT') ?: '3306';
$dbName = getenv('DB_NAME') ?: 'hotel';
$dbUser = getenv('DB_USER') ?: 'root';
$dbPass = getenv('DB_PASS') ?: 'root';

return [
    'db' => [
        'host' => $dbHost,
        'port' => $dbPort,
        'dbname' => $dbName,
        'user' => $dbUser,
        'password' => $dbPass,
        'charset' => 'utf8mb4',
    ],
    'app' => [
        'name' => 'Сладкие сны',
        'slogan' => 'Сладкие сны — ваш идеальный отдых',
        'contact_address' => 'г. Новокузнецк, ул. Садовая, д. 12',
        'contact_schedule' => 'круглосуточно',
        'contact_phone' => '+7(900)000-00-00',
        'contact_email' => 'info@sladkiesny.ru',
        'rooms_per_page' => 6,
    ],
];