<?php session_start(); ?>
<?php $catalogHref = (isset($_SESSION['logged_in']) && $_SESSION['logged_in']) ? 'products.php' : 'auth.php'; ?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/styles.css">
    <title>Магазин электроники</title>
</head>
<body>
    <?php require 'header.php'; ?>
    <main class="home-page">
        <section class="hero-section">
            <div class="hero-content">
                <p class="eyebrow">Премиальный Electro Shop</p>
                <h1>Современная техника для работы, игр и повседневной жизни</h1>
                <p class="hero-lead">
                    Лучшие гаджеты, ноутбуки, смартфоны и аксессуары с быстрой доставкой,
                    понятным выбором и аккуратным сервисом.
                </p>

                <div class="hero-actions">
                    <a class="btn btn-primary" href="<?= $catalogHref ?>">В каталог</a>
                    <a class="btn btn-secondary" href="products.php?sort=price_asc">Лучшие предложения</a>
                </div>

                <div class="hero-badges" aria-label="Преимущества магазина">
                    <span>Best Quality</span>
                    <span>Fast Shipping</span>
                    <span>Secure Payment</span>
                </div>
            </div>

            <div class="hero-showcase" aria-label="Популярная электроника">
                <div class="showcase-card showcase-card-large">
                    <img src="images/macbook.jpg" alt="MacBook Air M2">
                    <div>
                        <span>Ноутбуки</span>
                        <strong>MacBook Air M2</strong>
                    </div>
                </div>

                <div class="showcase-card showcase-card-phone">
                    <img src="images/iphone15.webp" alt="iPhone 14 Pro">
                    <div>
                        <span>Смартфоны</span>
                        <strong>iPhone 14 Pro</strong>
                    </div>
                </div>

                <div class="showcase-card showcase-card-accent">
                    <img src="images/s23.webp" alt="Samsung Galaxy S23">
                    <div>
                        <span>Android</span>
                        <strong>Galaxy S23</strong>
                    </div>
                </div>

                <div class="showcase-stat">
                    <strong>3-7 дней</strong>
                    <span>доставка заказов</span>
                </div>
            </div>
        </section>

        <section class="category-section">
            <div class="section-heading">
                <p class="eyebrow">Категории</p>
                <h2>Выбирайте технику по задачам</h2>
            </div>

            <div class="category-grid">
                <a class="category-card" href="products.php?q=iphone">
                    <span>01</span>
                    <strong>Смартфоны</strong>
                    <p>Флагманы и повседневные модели.</p>
                </a>
                <a class="category-card" href="products.php?q=macbook">
                    <span>02</span>
                    <strong>Ноутбуки</strong>
                    <p>Лёгкие устройства для учёбы и работы.</p>
                </a>
                <a class="category-card" href="products.php?q=samsung">
                    <span>03</span>
                    <strong>Гаджеты</strong>
                    <p>Экосистема, аксессуары и обновления.</p>
                </a>
            </div>
        </section>

        <section class="benefit-strip">
            <article>
                <strong>Качество</strong>
                <p>Карточки товаров показывают реальные позиции из базы магазина.</p>
            </article>
            <article>
                <strong>Быстрый выбор</strong>
                <p>Поиск и сортировка помогают быстрее найти подходящую технику.</p>
            </article>
            <article>
                <strong>Личный кабинет</strong>
                <p>Заказы и профиль остаются доступны после авторизации.</p>
            </article>
        </section>
    </main>

</body>
</html>
