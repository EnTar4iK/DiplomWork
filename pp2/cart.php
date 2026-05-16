<?php
session_start();
require 'config/db.php';

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$cartItems = [];
$total = 0;

foreach ($_SESSION['cart'] as $id => $qty) {
    $productId = (int) $id;
    $quantity = (int) $qty;

    if ($productId <= 0 || $quantity <= 0) {
        continue;
    }

    $sql = "SELECT * FROM products WHERE id = $productId";
    $result = $conn->query($sql);
    $product = $result ? $result->fetch_assoc() : null;

    if (!$product) {
        continue;
    }

    $sum = (int) $product['price'] * $quantity;
    $total += $sum;
    $cartItems[] = [
        'product' => $product,
        'quantity' => $quantity,
        'sum' => $sum,
    ];
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Корзина</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>

<?php require 'header.php'; ?>

<main class="page-shell cart-page">
    <section class="page-hero compact-hero">
        <p class="eyebrow">Корзина</p>
        <h1>Проверьте выбранную технику</h1>
        <p>Количество, цена и итоговая сумма формируются из текущей сессии корзины.</p>
    </section>

    <?php if (empty($cartItems)): ?>
        <section class="empty-state">
            <h2>Корзина пока пуста</h2>
            <p>Добавьте товары из каталога, и они появятся здесь перед оформлением заказа.</p>
            <a href="products.php" class="btn btn-primary">Перейти в каталог</a>
        </section>
    <?php else: ?>
        <section class="cart-layout">
            <div class="cart-items">
                <?php foreach ($cartItems as $item): ?>
                    <?php $product = $item['product']; ?>
                    <article class="cart-item">
                        <img
                            src="images/<?= rawurlencode($product['image']) ?>"
                            alt="<?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8') ?>"
                        >

                        <div>
                            <span class="product-category">Электроника</span>
                            <h3><?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8') ?></h3>
                            <p>Цена: <?= number_format((int) $product['price'], 0, ',', ' ') ?> ₽</p>
                        </div>

                        <div class="cart-quantity">
                            <span>Количество</span>
                            <strong><?= (int) $item['quantity'] ?></strong>
                        </div>

                        <div class="cart-sum">
                            <span>Сумма</span>
                            <strong><?= number_format((int) $item['sum'], 0, ',', ' ') ?> ₽</strong>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>

            <aside class="cart-summary">
                <p class="eyebrow">Итого</p>
                <strong><?= number_format($total, 0, ',', ' ') ?> ₽</strong>
                <p>Заказ будет создан для авторизованного пользователя на следующем шаге.</p>
                <a href="checkout.php" class="btn btn-primary">Оформить заказ</a>
                <a href="products.php" class="btn btn-secondary">Продолжить покупки</a>
            </aside>
        </section>
    <?php endif; ?>
</main>

</body>
</html>
