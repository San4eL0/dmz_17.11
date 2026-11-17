-- ============================================================
-- Начальные данные (номера гостиницы «Сладкие сны»)
-- Выполнять после schema.sql (в уже созданной в InfinityFree базе)
-- ============================================================

INSERT INTO rooms
    (id, category, price, places, area, has_conditioner, has_tv, has_fridge, has_wifi, image)
VALUES
    (1, 'Стандарт', 3500.00, 2, 20.00, 1, 1, 0, 1, 'assets/img/room-standard-1.jpg'),
    (2, 'Стандарт', 3900.00, 2, 22.00, 1, 1, 1, 1, 'assets/img/room-standard-2.jpg'),
    (3, 'Студия',   6000.00, 2, 30.00, 1, 1, 1, 1, 'assets/img/room-studio-1.jpg'),
    (4, 'Студия',   6500.00, 3, 34.00, 1, 1, 1, 1, 'assets/img/room-studio-2.jpg'),
    (5, 'Люкс',     8500.00, 3, 45.00, 1, 1, 1, 1, 'assets/img/room-lux-1.jpg'),
    (6, 'Люкс',     9500.00, 4, 55.00, 1, 1, 1, 1, 'assets/img/room-lux-2.jpg');