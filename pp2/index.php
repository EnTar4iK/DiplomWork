<?php
session_start();
require 'config/db.php';
require_once 'functions.php';

$featuredProducts = fetch_featured_products($conn, 6);
$categories = fetch_categories($conn);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/styles.css">
    <title>DАЙКОМ Store — электроника, сервис и доставка</title>
</head>
<body>
    <?php require 'header.php'; ?>

    <main>
        <section class="hero">
            <div class="hero-grid">
                <div class="hero-copy">
                    <p class="eyebrow">Продажа и обслуживание компьютерной техники · г. Шахты</p>
                    <h1>Электроника, которая работает на ваш темп</h1>
                    <p>
                        DАЙКОМ — локальный магазин техники с 27+ годами опыта, реальными складами,
                        сервисным центром и доставкой по городу. Собрали витрину ноутбуков, ПК,
                        видеокарт, мониторов и аксессуаров с быстрым оформлением заказа.
                    </p>

                    <div class="hero-actions">
                        <a class="btn btn-primary" href="products.php">Смотреть каталог</a>
                        <a class="btn btn-glass" href="delivery.php">Оплата и доставка</a>
                    </div>

                    <div class="hero-stats">
                        <div><strong>27+</strong><span>лет на рынке</span></div>
                        <div><strong>2</strong><span>магазина в Шахтах</span></div>
                        <div><strong>24/7</strong><span>онлайн-заказ</span></div>
                    </div>
                </div>

                <div class="hero-card">
                    <span class="hero-card-badge">Хит недели</span>
                    <img src="https://images.unsplash.com/photo-1593642632823-8f785ba67e45?auto=format&fit=crop&w=900&q=85" alt="Современный ноутбук на рабочем столе">
                    <div>
                        <h2>Ноутбуки для учебы, офиса и игр</h2>
                        <p>Подберём модель, установим ПО, перенесём данные и доставим в удобное время.</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="trust-strip">
            <article>
                <span>01</span>
                <h3>Лучшие цены</h3>
                <p>Ежедневно сравниваем предложения крупных сетей и даём честную стоимость.</p>
            </article>
            <article>
                <span>02</span>
                <h3>Сервис рядом</h3>
                <p>Ремонтируем электронику, компьютеры, ноутбуки и оргтехнику в собственных мастерских.</p>
            </article>
            <article>
                <span>03</span>
                <h3>Быстрая выдача</h3>
                <p>Самовывоз из DАЙКОМ или «Компьютерный мир», доставка по Шахтам и отправка ТК.</p>
            </article>
        </section>

        <section class="section-block">
            <div class="section-heading">
                <p class="eyebrow">Категории</p>
                <h2>Популярные направления</h2>
                <a href="products.php">Все товары</a>
            </div>

            <div class="category-grid">
                <?php foreach ($categories as $category): ?>
                    <a class="category-card" href="products.php?category=<?= (int) $category['id'] ?>">
                        <span><?= h($category['name']) ?></span>
                        <small><?= h($category['description']) ?></small>
                    </a>
                <?php endforeach; ?>
            </div>
        </section>

        <section class="section-block">
            <div class="section-heading">
                <p class="eyebrow">Витрина</p>
                <h2>Товары с быстрым заказом</h2>
                <a href="products.php">Перейти в каталог</a>
            </div>

            <div class="products-grid">
                <?php foreach ($featuredProducts as $product): ?>
                    <article class="product-card">
                        <?php if (!empty($product['badge'])): ?>
                            <span class="product-badge"><?= h($product['badge']) ?></span>
                        <?php endif; ?>

                        <a class="product-media" href="product.php?id=<?= (int) $product['id'] ?>">
                            <img src="<?= product_image($product['image']) ?>" alt="<?= h($product['name']) ?>">
                        </a>

                        <div class="product-body">
                            <span class="product-category"><?= h($product['category_name']) ?></span>
                            <h3><a href="product.php?id=<?= (int) $product['id'] ?>"><?= h($product['name']) ?></a></h3>
                            <p><?= h($product['short_description']) ?></p>
                            <div class="product-footer">
                                <strong><?= money($product['price']) ?></strong>
                                <a class="btn btn-small" href="add_to_cart.php?id=<?= (int) $product['id'] ?>">В корзину</a>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </section>

        <section class="service-banner">
            <div>
                <p class="eyebrow">DАЙКОМ all-in-one</p>
                <h2>Не просто продаём — запускаем технику под ключ</h2>
                <p>
                    В одном месте: магазин, склад, сервисный центр, корпоративный отдел и доставка.
                    Поможем выбрать комплектующие, собрать системный блок, настроить сеть или
                    восстановить устройство после поломки.
                </p>
            </div>
            <a class="btn btn-primary" href="contacts.php">Связаться с менеджером</a>
        </section>
    </main>

    <?php require 'footer.php'; ?>
</body>
</html>