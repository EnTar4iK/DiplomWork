<?php
session_start();
require_once 'functions.php';
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Доставка и оплата — DАЙКОМ Store</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
<?php require 'header.php'; ?>

<main class="page-shell">
    <section class="page-hero">
        <p class="eyebrow">Оплата и доставка</p>
        <h1>Получайте технику так, как удобно вам</h1>
        <p>
            Заберите заказ в одном из торгово-сервисных центров, оформите курьерскую доставку
            по Шахтам или отправку транспортной компанией. Оплата доступна онлайн и при получении.
        </p>
    </section>

    <section class="info-grid">
        <article class="info-card">
            <span>Самовывоз</span>
            <h2>DАЙКОМ</h2>
            <p>г. Шахты, ул. Советская, 214-а. Пн–пт 9:00–18:00, сб–вс 9:00–17:00.</p>
        </article>
        <article class="info-card">
            <span>Самовывоз</span>
            <h2>Компьютерный мир</h2>
            <p>г. Шахты, пр. Победа Революции, 85. Пн–пт 9:00–18:00, сб–вс 9:00–17:00.</p>
        </article>
        <article class="info-card">
            <span>Курьер</span>
            <h2>Доставка по городу</h2>
            <p>Менеджер уточнит адрес, время и стоимость доставки после оформления заказа.</p>
        </article>
        <article class="info-card">
            <span>Россия</span>
            <h2>Транспортная компания</h2>
            <p>Отправка в другие города возможна после подтверждения оплаты и наличия товара.</p>
        </article>
    </section>

    <section class="steps-section">
        <div class="section-heading">
            <p class="eyebrow">Как это работает</p>
            <h2>Заказ за 4 шага</h2>
        </div>
        <div class="steps-grid">
            <article><strong>1</strong><h3>Выберите товар</h3><p>Сравните категории, фильтры и характеристики в каталоге.</p></article>
            <article><strong>2</strong><h3>Оформите корзину</h3><p>Укажите контакты, способ доставки и комментарий для менеджера.</p></article>
            <article><strong>3</strong><h3>Оплатите</h3><p>Карта онлайн, СБП, при получении или счёт для юридического лица.</p></article>
            <article><strong>4</strong><h3>Получите заказ</h3><p>Самовывоз, курьер по Шахтам или отправка транспортной компанией.</p></article>
        </div>
    </section>

    <section class="payment-panel">
        <div>
            <p class="eyebrow">Оплата</p>
            <h2>Поддерживаем все популярные сценарии</h2>
            <p>Для учебного проекта онлайн-эквайринг имитируется: заказ получает статус «Оплачен онлайн» без списания средств.</p>
        </div>
        <ul>
            <li>Банковская карта онлайн</li>
            <li>Система быстрых платежей</li>
            <li>Карта или наличные при получении</li>
            <li>Безналичный счёт для организаций</li>
        </ul>
    </section>
</main>

<?php require 'footer.php'; ?>
</body>
</html>
