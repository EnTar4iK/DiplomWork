<?php
session_start();
require_once 'functions.php';
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Контакты — DАЙКОМ Store</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
<?php require 'header.php'; ?>

<main class="page-shell">
    <section class="page-hero">
        <p class="eyebrow">Контакты</p>
        <h1>Мы рядом в Шахтах</h1>
        <p>
            ООО Фирма «Дайком», ИНН 6155021283, ОГРН 1026102774510.
            Продажа, ремонт и обслуживание компьютерной техники и электроники.
        </p>
    </section>

    <section class="contact-grid">
        <article class="contact-card primary">
            <span>Главный офис</span>
            <h2>Торгово-сервисный центр DАЙКОМ</h2>
            <p>Ростовская область, г. Шахты, ул. Советская, 214-а</p>
            <p>Пн–пт 9:00–18:00, сб–вс 9:00–17:00</p>
            <a href="tel:+79185112333">+7 918 511-23-33</a>
            <a href="tel:+79185092333">+7 918 509-23-33</a>
        </article>

        <article class="contact-card">
            <span>Магазин</span>
            <h2>Компьютерный мир</h2>
            <p>г. Шахты, пр. Победа Революции, 85</p>
            <p>Пн–пт 9:00–18:00, сб–вс 9:00–17:00</p>
            <a href="tel:+79185515775">+7 918 551-57-75</a>
        </article>

        <article class="contact-card">
            <span>Email</span>
            <h2>Онлайн-связь</h2>
            <p><a href="mailto:office@daycom.ru">office@daycom.ru</a></p>
            <p>Директор: Кулиниченко Максим Вячеславович</p>
            <p><a href="mailto:director@daycom.ru">director@daycom.ru</a></p>
        </article>
    </section>

    <section class="map-card">
        <div>
            <p class="eyebrow">Локация</p>
            <h2>Адрес для навигатора</h2>
            <p>г. Шахты, ул. Советская, 214-а</p>
        </div>
        <a class="btn btn-primary" href="https://yandex.ru/maps/?text=г.%20Шахты%2C%20ул.%20Советская%2C%20214-а" target="_blank" rel="noopener">Открыть карту</a>
    </section>
</main>

<?php require 'footer.php'; ?>
</body>
</html>
