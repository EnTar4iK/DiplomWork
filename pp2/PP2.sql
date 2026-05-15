-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1:3306
-- Время создания: Апр 09 2026 г., 18:13
-- Версия сервера: 8.0.30
-- Версия PHP: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `PP2`
--

-- --------------------------------------------------------

--
-- Структура таблицы `orders`
--

CREATE TABLE `orders` (
  `id` int NOT NULL,
  `user_id` int DEFAULT NULL,
  `product_id` int DEFAULT NULL,
  `quantity` int DEFAULT '1',
  `total_price` int DEFAULT NULL,
  `status` varchar(50) DEFAULT 'new',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `product_id`, `quantity`, `total_price`, `status`, `created_at`) VALUES
(1, 1, 1, 1, 109990, 'new', '2026-04-09 11:58:43'),
(2, 1, 2, 1, 89990, 'new', '2026-04-09 11:58:43'),
(3, 9, 2, 1, 89990, 'new', '2026-04-09 11:59:08'),
(4, 9, 1, 1, 109990, 'new', '2026-04-09 11:59:08');

-- --------------------------------------------------------

--
-- Структура таблицы `products`
--

CREATE TABLE `products` (
  `id` int UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `price` int NOT NULL,
  `description` text NOT NULL,
  `image` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `products`
--

INSERT INTO `products` (`id`, `name`, `price`, `description`, `image`) VALUES
(1, 'iPhone 14 Pro', 109990, 'Apple iPhone 14 Pro оснащён 6.1-дюймовым дисплеем Super Retina XDR с ProMotion 120 Гц. Чип A16 Bionic обеспечивает высокую производительность, а камера 48 МП позволяет делать профессиональные фото и видео.', 'iphone15.webp'),
(2, 'Samsung Galaxy S23', 89990, 'Samsung Galaxy S23 — флагман с мощным процессором Snapdragon 8 Gen 2, AMOLED 120 Гц дисплеем и камерой 50 МП. Отличный выбор для игр и фото.', 's23.webp'),
(3, 'MacBook Air M2', 134990, 'MacBook Air на чипе Apple M2 — лёгкий и мощный ноутбук с 13.6-дюймовым дисплеем Liquid Retina, тихой работой и высокой автономностью до 18 часов.', 'macbook.jpg'),
(4, 'iPhone 16 pro', 83500, 'iPhone, Кабель USB-C(1 м), Документация', 'i16.jpg');

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE `users` (
  `id` int UNSIGNED NOT NULL,
  `login` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `telephone` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `role` varchar(55) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `users`
--

INSERT INTO `users` (`id`, `login`, `password`, `telephone`, `role`) VALUES
(1, 'admin', 'admin', '+7 999 993 54 65', 'admin'),
(4, 'Егор', '12345', '', 'user'),
(5, 'Александр', '12345', '', 'user'),
(6, 'Богдан', '12345', '', 'user'),
(7, 'Антон', '12345', '', 'user'),
(9, 'Kovalev', '12345', '', 'admin');

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT для таблицы `products`
--
ALTER TABLE `products`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT для таблицы `users`
--
ALTER TABLE `users`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
