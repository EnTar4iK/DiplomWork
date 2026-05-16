<?php
session_start();
require 'config/db.php';

if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    header("Location: auth.php");
    exit();
}

$userLogin = $_SESSION['username'];
$userRes = $conn->query("SELECT id FROM users WHERE login = '$userLogin'");
$user = $userRes->fetch_assoc();
$user_id = $user['id'];
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Мои заказы</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>

<?php require 'header.php'; ?>

<?php
$sql = "SELECT o.*, p.name
        FROM orders o
        JOIN products p ON o.product_id = p.id
        WHERE o.user_id = $user_id
        ORDER BY o.id DESC";

$result = $conn->query($sql);
$orders = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
?>

<main class="page-shell">
    <section class="page-hero compact-hero">
        <p class="eyebrow">История покупок</p>
        <h1>Мои заказы</h1>
        <p>Здесь отображаются заказы, созданные через корзину текущего аккаунта.</p>
    </section>

    <?php if (empty($orders)): ?>
        <section class="empty-state">
            <h2>Заказов пока нет</h2>
            <p>Перейдите в каталог, добавьте технику в корзину и оформите первый заказ.</p>
            <a class="btn btn-primary" href="products.php">Перейти в каталог</a>
        </section>
    <?php else: ?>
        <section class="order-list">
            <?php foreach ($orders as $row): ?>
                <article class="order-card">
                    <span class="product-category">Заказ #<?= (int) $row['id'] ?></span>
                    <h3><?= htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8') ?></h3>
                    <div class="order-meta">
                        <p>Количество: <?= (int) $row['quantity'] ?></p>
                        <p>Сумма: <?= number_format((int) $row['total_price'], 0, ',', ' ') ?> ₽</p>
                        <p>Дата: <?= htmlspecialchars($row['created_at'], ENT_QUOTES, 'UTF-8') ?></p>
                    </div>
                </article>
            <?php endforeach; ?>
        </section>
    <?php endif; ?>
</main>

</body>
</html>
