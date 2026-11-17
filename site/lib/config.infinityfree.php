<?php
/**
 * ПРИМЕР конфигурации для InfinityFree.
 *
 * Как использовать:
 * 1. В панели InfinityFree создайте базу данных MySQL (напр. youruser_hotel).
 * 2. Скопируйте этот файл в site/lib/config.local.php ИЛИ задайте переменные
 *    окружения, указанные в lib/config.php (рекомендуется переменные).
 *
 * InfinityFree обычно предоставляет доступ к БД:
 *   Хост: sqlXXX.infinityfree.com  (см. вкладку MySQL Databases)
 *   База: youruser_hotel
 *   Пользователь: youruser
 *   Пароль: тот, что задали при создании базы.
 *
 * Указать это можно двумя способами:
 *   a) Через файл .htaccess в корне сайта:
 *        SetEnv DB_HOST sqlXXX.infinityfree.com
 *        SetEnv DB_NAME youruser_hotel
 *        SetEnv DB_USER youruser
 *        SetEnv DB_PASS YOUR_DB_PASSWORD
 *
 *   б) Либо отредактировать lib/config.php напрямую (значения по умолчанию).
 *
 * Безопасность: НЕ коммитьте реальные пароли в репозиторий — используйте
 * переменные окружения через .htaccess (файл не загружается на GitHub).
 */
$dbHost = getenv('DB_HOST') ?: 'sqlXXX.infinityfree.com';
$dbName = getenv('DB_NAME') ?: 'youruser_hotel';
$dbUser = getenv('DB_USER') ?: 'youruser';
$dbPass = getenv('DB_PASS') ?: 'YOUR_DATABASE_PASSWORD';

putenv("DB_HOST=$dbHost");
putenv("DB_NAME=$dbName");
putenv("DB_USER=$dbUser");
putenv("DB_PASS=$dbPass");