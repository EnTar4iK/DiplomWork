<?php
session_start();
require 'config/db.php';
require_once 'functions.php';

require_login();

$user = current_user($conn);
$statuses = order_statuses();
$orders = [];

if ($user) {
    $userId = (int) $user['id'];
    $result = $conn->query("SELECT * FROM orders WHERE user_id = $userId ORDER BY id DESC");
    $orders = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Мои заказы — DАЙКОМ Store</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>

<?php require 'header.php'; ?>

<main class="page-shell">
    <section class="page-hero compact">
        <p class="eyebrow">Личный кабинет</p>
        <h1>Мои заказы</h1>
        <p>История покупок, статусы оплаты и доставки.</p>
    </section>

    <?php if (isset($_GET['created'])): ?>
        <div class="message-box success">Заказ #<?= (int) $_GET['created'] ?> создан. Мы скоро свяжемся с вами.</div>
    <?php endif; ?>

    <?php if (empty($orders)): ?>
        <section class="empty-state">
            <h2>Заказов пока нет</h2>
            <p>Оформите первый заказ из каталога DАЙКОМ.</p>
            <a class="btn btn-primary" href="products.php">В каталог</a>
        </section>
    <?php else: ?>
        <section class="orders-list">
            <?php foreach ($orders as $order): ?>
                <?php
                $orderId = (int) $order['id'];
                $itemsResult = $conn->query("SELECT * FROM order_items WHERE order_id = $orderId");
                $items = $itemsResult ? $itemsResult->fetch_all(MYSQLI_ASSOC) : [];
                $status = (string) $order['status'];
                ?>
                <article class="order-card">
                    <div class="order-head">
                        <div>
                            <span class="product-category">Заказ #<?= $orderId ?></span>
                            <h2><?= money($order['total_price']) ?></h2>
                            <p><?= h($order['created_at']) ?></p>
                        </div>
                        <span class="status-badge status-<?= h($status) ?>"><?= h($statuses[$status] ?? $status) ?></span>
                    </div>

                    <div class="order-meta">
                        <p>Доставка: <?= h(delivery_methods()[$order['delivery_method']] ?? $order['delivery_method']) ?></p>
                        <p>Оплата: <?= h(payment_methods()[$order['payment_method']] ?? $order['payment_method']) ?></p>
                        <?php if (!empty($order['delivery_address'])): ?>
                            <p>Адрес: <?= h($order['delivery_address']) ?></p>
                        <?php endif; ?>
                    </div>

                    <div class="order-items">
                        <?php foreach ($items as $item): ?>
                            <div>
                                <span><?= h($item['product_name']) ?> × <?= (int) $item['quantity'] ?></span>
                                <strong><?= money($item['total_price']) ?></strong>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </article>
            <?php endforeach; ?>
        </section>
    <?php endif; ?>
</main>

<?php require 'footer.php'; ?>

</body>
</html>
