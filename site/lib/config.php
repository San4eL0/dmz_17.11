<?php
/**
 * Конфигурация сервиса «Сладкие сны»
 */
return [
    'db' => [
        'host' => '127.0.0.1',
        'port' => 3306,
        'dbname' => 'hotel',
        'user' => 'root',
        'password' => 'root',
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