-- phpMyAdmin SQL Dump
-- База данных: `PP2`
-- Проект: DАЙКОМ Store — учебный интернет-магазин электроники на PHP/MySQL

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";
/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

DROP TABLE IF EXISTS `order_items`;
DROP TABLE IF EXISTS `orders`;
DROP TABLE IF EXISTS `products`;
DROP TABLE IF EXISTS `categories`;
DROP TABLE IF EXISTS `users`;

CREATE TABLE `categories` (
  `id` int UNSIGNED NOT NULL,
  `name` varchar(120) NOT NULL,
  `description` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `products` (
  `id` int UNSIGNED NOT NULL,
  `category_id` int UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `price` int NOT NULL,
  `short_description` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `specs` json DEFAULT NULL,
  `image` varchar(500) NOT NULL,
  `stock` int NOT NULL DEFAULT '0',
  `badge` varchar(80) DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `users` (
  `id` int UNSIGNED NOT NULL,
  `login` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `telephone` varchar(30) DEFAULT NULL,
  `role` varchar(55) NOT NULL DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `orders` (
  `id` int UNSIGNED NOT NULL,
  `user_id` int UNSIGNED NOT NULL,
  `customer_name` varchar(255) NOT NULL,
  `phone` varchar(30) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `delivery_method` varchar(60) NOT NULL,
  `payment_method` varchar(60) NOT NULL,
  `delivery_address` text,
  `comment` text,
  `total_price` int NOT NULL,
  `status` varchar(50) DEFAULT 'new',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `order_items` (
  `id` int UNSIGNED NOT NULL,
  `order_id` int UNSIGNED NOT NULL,
  `product_id` int UNSIGNED DEFAULT NULL,
  `product_name` varchar(255) NOT NULL,
  `price` int NOT NULL,
  `quantity` int NOT NULL,
  `total_price` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `categories` (`id`, `name`, `description`) VALUES
(1, 'Ноутбуки', 'Acer, Asus, MSI, Digma, F+ и Azerty для учёбы, офиса и игр'),
(2, 'Компьютеры', 'Готовые системные блоки и компактные неттопы'),
(3, 'Мониторы', 'Full HD и игровые дисплеи для дома и рабочего места'),
(4, 'Видеокарты', 'GeForce, Radeon и Intel ARC для апгрейда ПК'),
(5, 'SSD и накопители', 'M.2, SATA и внешние диски для быстрого хранения данных'),
(6, 'Периферия', 'Клавиатуры, мыши, гарнитуры, акустика и аксессуары'),
(7, 'Компьютерная мебель', 'Офисные и игровые кресла для комфортной работы');

INSERT INTO `products` (`id`, `category_id`, `name`, `price`, `short_description`, `description`, `specs`, `image`, `stock`, `badge`) VALUES
(1, 1, 'Ноутбук Acer Nitro ANV15-51-53A7', 89999, 'Игровой 15.6" ноутбук с Intel Core i5, RTX 3050 6 ГБ и быстрым SSD.', 'Acer Nitro — производительная модель для игр, учёбы, монтажа и 3D-задач. Подходит тем, кому нужен мобильный компьютер с дискретной графикой и запасом мощности. DАЙКОМ поможет установить систему, драйверы и базовый набор программ.', '{"Процессор":"Intel Core i5-13420H","Память":"16 ГБ","Накопитель":"512 ГБ SSD","Графика":"GeForce RTX 3050 6 ГБ","Экран":"15.6 FHD"}', 'https://images.unsplash.com/photo-1593642632823-8f785ba67e45?auto=format&fit=crop&w=1200&q=85', 3, 'Хит'),
(2, 1, 'Ноутбук ASUS X1504ZA-BQ1143', 43999, 'Лёгкий универсальный ноутбук на Intel Core i3 для офиса и учёбы.', 'ASUS X1504ZA — сбалансированный ноутбук для документов, браузера, видеосвязи и повседневных задач. Хороший выбор для студентов, школьников и удалённой работы.', '{"Процессор":"Intel Core i3-1215U","Память":"8 ГБ","Накопитель":"512 ГБ SSD","Экран":"15.6 FHD","ОС":"noOS"}', 'https://images.unsplash.com/photo-1496181133206-80ce9b88a853?auto=format&fit=crop&w=1200&q=85', 5, 'Оптимальный'),
(3, 1, 'Ноутбук MSI Thin 15 B13VE', 92999, 'Тонкий игровой ноутбук с Core i7 и RTX 4050 для работы и развлечений.', 'MSI Thin 15 подойдёт для современных игр, инженерных приложений, графики и многозадачности. Корпус остаётся мобильным, а производительность закрывает большинство сценариев на несколько лет.', '{"Процессор":"Intel Core i7-13620H","Память":"16 ГБ","Накопитель":"512 ГБ SSD","Графика":"GeForce RTX 4050 6 ГБ","Экран":"15.6 FHD"}', 'https://images.unsplash.com/photo-1603302576837-37561b2e2302?auto=format&fit=crop&w=1200&q=85', 2, 'Pro'),
(4, 4, 'Видеокарта GeForce RTX 3050 Gigabyte WindForce 2 OC', 28499, '8 ГБ видеопамяти, тихое охлаждение и поддержка актуальных технологий NVIDIA.', 'RTX 3050 — разумный апгрейд для Full HD-гейминга, работы с графикой и ускорения творческих приложений. Перед покупкой специалисты DАЙКОМ проверят совместимость с блоком питания и корпусом.', '{"Память":"8 ГБ","Серия":"GeForce RTX 3050","Охлаждение":"WindForce 2","Интерфейсы":"DP, HDMI","Гарантия":"по условиям магазина"}', 'https://images.unsplash.com/photo-1591488320449-011701bb6704?auto=format&fit=crop&w=1200&q=85', 4, 'Апгрейд'),
(5, 4, 'Видеокарта Intel ARC B580 ASRock Challenger OC', 33999, '12 ГБ видеопамяти для новых игр, стриминга и рабочих задач.', 'Intel ARC B580 — современная видеокарта с большим объёмом памяти и актуальными медиаблоками. Хорошо подходит для сборок с прицелом на новые игры и создание контента.', '{"Память":"12 ГБ","Серия":"Intel ARC B580","Версия":"Challenger OC","Назначение":"игры, стриминг, монтаж"}', 'https://images.unsplash.com/photo-1587202372775-e229f172b9d7?auto=format&fit=crop&w=1200&q=85', 2, 'New'),
(6, 3, 'Монитор 24" IPS Full HD 100 Гц', 10990, 'Яркий IPS-дисплей с плавной картинкой для офиса, учёбы и дома.', 'Универсальный монитор с комфортной диагональю, широкими углами обзора и частотой 100 Гц. Подходит для рабочего места, домашнего ПК и второго экрана к ноутбуку.', '{"Диагональ":"24 дюйма","Матрица":"IPS","Разрешение":"1920×1080","Частота":"100 Гц","Порты":"HDMI, VGA"}', 'https://images.unsplash.com/photo-1527443224154-c4a3942d3acf?auto=format&fit=crop&w=1200&q=85', 8, 'В наличии'),
(7, 3, 'Игровой монитор 27" QHD 165 Гц', 25990, 'Большой QHD-экран с высокой частотой обновления для игр и графики.', 'Монитор 27" QHD раскрывает современные видеокарты, даёт больше пространства для работы и плавность в динамичных сценах. Отличный выбор для игровой зоны и домашней студии.', '{"Диагональ":"27 дюймов","Разрешение":"2560×1440","Частота":"165 Гц","Матрица":"IPS","Синхронизация":"Adaptive Sync"}', 'https://images.unsplash.com/photo-1616588589676-62b3bd4ff6d2?auto=format&fit=crop&w=1200&q=85', 3, 'Gaming'),
(8, 5, 'SSD M.2 NVMe 1 ТБ Kingston NV3', 7390, 'Быстрый накопитель M.2 для апгрейда ноутбука или системного блока.', 'SSD M.2 NVMe заметно ускоряет запуск Windows, программ и игр. Специалисты DАЙКОМ помогут перенести данные со старого диска и установить накопитель.', '{"Форм-фактор":"M.2 2280","Интерфейс":"NVMe PCIe","Объём":"1 ТБ","Назначение":"система, игры, рабочие файлы"}', 'https://images.unsplash.com/photo-1597872200969-2b65d56bd16b?auto=format&fit=crop&w=1200&q=85', 12, 'Быстро'),
(9, 5, 'Внешний SSD 1 ТБ USB-C', 8990, 'Компактное хранилище для фото, видео, резервных копий и рабочих проектов.', 'Внешний SSD удобно брать с собой, использовать для быстрых резервных копий и переноса больших файлов. Подключается к современным ноутбукам, ПК и части смартфонов.', '{"Объём":"1 ТБ","Подключение":"USB-C","Назначение":"резервные копии, перенос данных","Корпус":"компактный"}', 'https://images.unsplash.com/photo-1611174743420-3d7df880ce32?auto=format&fit=crop&w=1200&q=85', 6, ''),
(10, 2, 'Системный блок DАЙКОМ Office Pro', 45990, 'Готовый офисный ПК с SSD, быстрым запуском и тихой работой.', 'Системный блок для бухгалтерии, документооборота, CRM, учёбы и интернет-задач. Подходит организациям: подготовим счёт, установим ПО и подключим периферию.', '{"Процессор":"Intel Core i3 / Ryzen 3","Память":"16 ГБ","Накопитель":"512 ГБ SSD","Сеть":"Gigabit LAN","Назначение":"офис, учёба"}', 'https://images.unsplash.com/photo-1587831990711-23ca6441447b?auto=format&fit=crop&w=1200&q=85', 4, 'Для офиса'),
(11, 6, 'Комплект Logitech: клавиатура и мышь', 3290, 'Надёжный беспроводной набор для рабочего места без лишних проводов.', 'Комплект клавиатуры и мыши для дома и офиса. Удобен для подключения к ноутбуку, моноблоку или системному блоку, не занимает USB-порты лишними проводами.', '{"Тип":"беспроводной комплект","Подключение":"USB-ресивер","Назначение":"офис, дом","Питание":"батарейки"}', 'https://images.unsplash.com/photo-1587829741301-dc798b83add3?auto=format&fit=crop&w=1200&q=85', 15, ''),
(12, 6, 'Игровая гарнитура HyperSound RGB', 4490, 'Объёмный звук, микрофон и мягкие амбушюры для долгих игровых сессий.', 'Гарнитура для игр, звонков и онлайн-обучения. Закрытая конструкция помогает сосредоточиться, а микрофон подходит для голосовых чатов и конференций.', '{"Подключение":"USB / 3.5 мм","Микрофон":"поворотный","Подсветка":"RGB","Назначение":"игры, звонки"}', 'https://images.unsplash.com/photo-1599669454699-248893623440?auto=format&fit=crop&w=1200&q=85', 9, 'RGB'),
(13, 7, 'Кресло игровое Zombie 11LT Red', 6920, 'Яркое игровое кресло с удобной посадкой для компьютера и консоли.', 'Кресло для домашнего игрового места, учебной зоны или офиса. Помогает комфортно проводить долгие сессии за компьютером.', '{"Тип":"игровое кресло","Цвет":"чёрный/красный","Материал":"ткань/экокожа","Назначение":"дом, игры, офис"}', 'https://images.unsplash.com/photo-1598300042247-d088f8ab3a91?auto=format&fit=crop&w=1200&q=85', 3, 'Комфорт'),
(14, 2, 'Неттоп Mini PC для офиса', 29990, 'Компактный компьютер для рабочего места, кассы или ресепшена.', 'Мини-ПК занимает минимум места, тихо работает и подходит для браузера, офисных программ, CRM и торговых задач. Легко крепится за монитором.', '{"Формат":"Mini PC","Память":"8 ГБ","Накопитель":"256 ГБ SSD","Назначение":"офис, касса, ресепшен"}', 'https://images.unsplash.com/photo-1591799264318-7e6ef8ddb7ea?auto=format&fit=crop&w=1200&q=85', 4, 'Компактно');

INSERT INTO `users` (`id`, `login`, `password`, `telephone`, `role`) VALUES
(1, 'admin', 'admin', '+7 999 993 54 65', 'admin'),
(2, 'client', '12345', '+7 918 511-23-33', 'user');

INSERT INTO `orders` (`id`, `user_id`, `customer_name`, `phone`, `email`, `delivery_method`, `payment_method`, `delivery_address`, `comment`, `total_price`, `status`, `created_at`) VALUES
(1, 2, 'Тестовый клиент', '+7 918 511-23-33', 'client@example.com', 'pickup_daycom', 'card_online', '', 'Демо-заказ для проверки админ-панели', 119988, 'paid', '2026-05-15 12:00:00');

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `product_name`, `price`, `quantity`, `total_price`) VALUES
(1, 1, 1, 'Ноутбук Acer Nitro ANV15-51-53A7', 89999, 1, 89999),
(2, 1, 6, 'Монитор 24" IPS Full HD 100 Гц', 10990, 1, 10990),
(3, 1, 11, 'Комплект Logitech: клавиатура и мышь', 3290, 1, 3290),
(4, 1, 12, 'Игровая гарнитура HyperSound RGB', 4490, 1, 4490);

ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `products_category_id` (`category_id`);

ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_login_unique` (`login`);

ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `orders_user_id` (`user_id`);

ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_items_order_id` (`order_id`),
  ADD KEY `order_items_product_id` (`product_id`);

ALTER TABLE `categories`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

ALTER TABLE `products`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

ALTER TABLE `users`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

ALTER TABLE `orders`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

ALTER TABLE `order_items`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

ALTER TABLE `products`
  ADD CONSTRAINT `products_category_fk` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

ALTER TABLE `orders`
  ADD CONSTRAINT `orders_user_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_order_fk` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `order_items_product_fk` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

COMMIT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
