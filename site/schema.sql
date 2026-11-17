-- ============================================================
-- Сервис бронирования номеров гостиницы «Сладкие сны»
-- Схема базы данных (MySQL)
-- ДЭ 09.02.09 / задания 3, 4
-- ============================================================

DROP DATABASE IF EXISTS hotel;
CREATE DATABASE hotel CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE hotel;

-- Каталог номеров
CREATE TABLE rooms (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category VARCHAR(50) NOT NULL COMMENT 'категория: стандарт, студия, люкс',
    price DECIMAL(10,2) NOT NULL COMMENT 'цена за сутки',
    places INT NOT NULL DEFAULT 2 COMMENT 'количество мест',
    area DECIMAL(6,2) NOT NULL COMMENT 'площадь, кв. м',
    has_conditioner TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'кондиционер',
    has_tv TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'телевизор',
    has_fridge TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'холодильник',
    has_wifi TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Wi-Fi',
    image VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'изображение',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Заявки на бронирование
CREATE TABLE bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    room_id INT NOT NULL,
    first_name VARCHAR(100) NOT NULL COMMENT 'имя',
    last_name VARCHAR(100) NOT NULL COMMENT 'фамилия',
    phone VARCHAR(30) NOT NULL COMMENT 'телефон',
    email VARCHAR(150) NOT NULL COMMENT 'e-mail',
    date_in DATE NOT NULL COMMENT 'дата заезда',
    date_out DATE NOT NULL COMMENT 'дата выезда',
    status ENUM('Новая', 'Одобрена', 'Отклонена') NOT NULL DEFAULT 'Новая',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (room_id) REFERENCES rooms(id) ON DELETE CASCADE,
    INDEX idx_room_dates (room_id, date_in, date_out),
    INDEX idx_status (status)
) ENGINE=InnoDB;

-- Администратор
CREATE TABLE admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    login VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Учётная запись администратора по умолчанию:
-- логин hotel123, пароль adminHotel (хэш ниже — bcrypt)
INSERT INTO admins (login, password_hash) VALUES
('hotel123', '$2b$10$vIsBwDqj3q1igW.ibS0Jf.su553VyIwH75CpuMnYdtejoHHRWy9Em');