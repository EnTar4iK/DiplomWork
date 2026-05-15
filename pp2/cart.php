<?php
session_start();
require 'config/db.php';
require_once 'functions.php';

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$cart = get_cart_items($conn);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Корзина — DАЙКОМ Store</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>

<?php require 'header.php'; ?>

<main class="page-shell">
    <section class="page-hero compact">
        <p class="eyebrow">Корзина</p>
        <h1>Проверьте заказ перед оформлением</h1>
        <p>Можно изменить количество, удалить позиции и перейти к выбору доставки и оплаты.</p>
    </section>

    <?php if (empty($cart['items'])): ?>
        <section class="empty-state">
            <h2>Корзина пуста</h2>
            <p>Добавьте товары из каталога — заказ сохранится в текущей сессии.</p>
            <a class="btn btn-primary" href="products.php">Перейти в каталог</a>
        </section>
    <?php else: ?>
        <form method="POST" action="update_cart.php" class="cart-layout">
            <section class="cart-list">
                <?php foreach ($cart['items'] as $item): ?>
                    <article class="cart-item">
                        <img src="<?= product_image($item['image']) ?>" alt="<?= h($item['name']) ?>">
                        <div>
                            <span class="product-category"><?= h($item['category_name']) ?></span>
                            <h3><a href="product.php?id=<?= (int) $item['id'] ?>"><?= h($item['name']) ?></a></h3>
                            <p><?= h($item['short_description']) ?></p>
                            <a class="remove-link" href="remove_from_cart.php?id=<?= (int) $item['id'] ?>">Удалить</a>
                        </div>
                        <div class="cart-controls">
                            <label>
                                Кол-во
                                <input type="number" name="quantity[<?= (int) $item['id'] ?>]" value="<?= (int) $item['quantity'] ?>" min="0" max="99">
                            </label>
                            <strong><?= money($item['line_total']) ?></strong>
                        </div>
                    </article>
                <?php endforeach; ?>
            </section>

            <aside class="cart-summary">
                <h2>Итого</h2>
                <div class="summary-row">
                    <span>Товары</span>
                    <strong><?= money($cart['total']) ?></strong>
                </div>
                <div class="summary-row">
                    <span>Доставка</span>
                    <strong>уточняется</strong>
                </div>
                <p>Менеджер подтвердит наличие, стоимость доставки и удобное время выдачи.</p>
                <button class="btn btn-glass" type="submit">Обновить корзину</button>
                <a class="btn btn-primary" href="checkout.php">Оформить заказ</a>
            </aside>
        </form>
    <?php endif; ?>
</main>

<?php require 'footer.php'; ?>

</body>
</html>