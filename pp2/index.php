<?php
session_start();
require 'config/db.php';
require_once 'functions.php';

$featuredProducts = fetch_featured_products($conn, 6);
$categories = fetch_categories($conn);
$trustFeatures = trust_features();
$checkoutSteps = checkout_steps();
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
                    <div class="hero-kicker">
                        <span>Daycom inspired</span>
                        <span>PHP + MySQL</span>
                        <span>2026 UI</span>
                    </div>
                    <p class="eyebrow">Продажа и обслуживание компьютерной техники · г. Шахты</p>
                    <h1>Техника, сервис и доставка в одном цифровом магазине</h1>
                    <p>
                        DАЙКОМ Store превращает локальный компьютерный магазин в полноценный ecommerce:
                        витрина электроники, реальные категории, быстрый checkout, понятная доставка,
                        оплата картой/СБП/счётом и поддержка сервисного центра.
                    </p>

                    <div class="hero-actions">
                        <a class="btn btn-primary" href="products.php">Смотреть каталог</a>
                        <a class="btn btn-glass" href="delivery.php">Оплата и доставка</a>
                    </div>

                    <div class="hero-stats">
                        <?php foreach (array_slice($trustFeatures, 0, 3) as $feature): ?>
                            <div>
                                <strong><?= h($feature[0]) ?></strong>
                                <span><?= h($feature[1]) ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="hero-card">
                    <span class="hero-card-badge">Хит недели</span>
                    <img src="https://images.unsplash.com/photo-1593642632823-8f785ba67e45?auto=format&fit=crop&w=900&q=85" alt="Современный ноутбук на рабочем столе">
                    <div class="hero-card-content">
                        <h2>Ноутбуки для учебы, офиса и игр</h2>
                        <p>Подберём модель, установим ПО, перенесём данные и доставим в удобное время.</p>
                        <div class="hero-card-price">
                            <span>от 43 999 ₽</span>
                            <a href="products.php?category=1">Подобрать</a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="trust-strip">
            <?php foreach ($trustFeatures as $feature): ?>
                <article>
                    <span><?= h($feature[0]) ?></span>
                    <h3><?= h($feature[1]) ?></h3>
                    <p><?= h($feature[2]) ?></p>
                </article>
            <?php endforeach; ?>
        </section>

        <section class="section-block">
            <div class="section-heading">
                <div>
                    <p class="eyebrow">Категории</p>
                    <h2>Покупатель сразу понимает, куда идти</h2>
                </div>
                <a class="section-link" href="products.php">Все товары</a>
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
                <div>
                    <p class="eyebrow">Витрина</p>
                    <h2>Карточки продают пользу, а не просто цену</h2>
                </div>
                <a class="section-link" href="products.php">Перейти в каталог</a>
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
                            <div class="stock-line">
                                <span><?= (int) $product['stock'] > 0 ? 'В наличии' : 'Под заказ' ?></span>
                                <span><?= h($product['category_name']) ?></span>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </section>

        <section class="section-block">
            <div class="section-heading">
                <div>
                    <p class="eyebrow">Путь заказа</p>
                    <h2>Checkout без лишних шагов</h2>
                </div>
                <a class="section-link" href="cart.php">Открыть корзину</a>
            </div>
            <div class="steps-grid">
                <?php foreach ($checkoutSteps as $step): ?>
                    <article>
                        <strong><?= h($step[0]) ?></strong>
                        <h3><?= h($step[1]) ?></h3>
                        <p><?= h($step[2]) ?></p>
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