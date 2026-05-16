<?php
require 'auth_admin.php';
require 'config/db.php';
require_once 'functions.php';

function fetchTotal(mysqli $conn, string $sql): int
{
    $result = $conn->query($sql);

    if ($result && ($row = $result->fetch_assoc())) {
        return (int) $row['total'];
    }

    return 0;
}

$stats = [
    'products' => fetchTotal($conn, "SELECT COUNT(*) AS total FROM products"),
    'orders' => fetchTotal($conn, "SELECT COUNT(*) AS total FROM orders"),
    'users' => fetchTotal($conn, "SELECT COUNT(*) AS total FROM users"),
    'newOrders' => fetchTotal($conn, "SELECT COUNT(*) AS total FROM orders WHERE status IN ('new', 'paid')"),
    'revenue' => fetchTotal($conn, "SELECT COALESCE(SUM(total_price), 0) AS total FROM orders WHERE status <> 'cancelled'"),
];
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Админ панель</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>

<?php require 'header.php'; ?>

<div class="page-shell admin-page">
    <section class="admin-hero">
        <div>
            <div class="hero-kicker">
                <span>Каталог</span>
                <span>Продажи</span>
                <span>Пользователи</span>
            </div>
            <p class="admin-eyebrow">Панель управления</p>
            <h2>Админ-панель DАЙКОМ Store</h2>
            <p class="admin-lead">
                Управляйте каталогом, заказами с оплатой и доставкой, пользователями и витриной магазина.
            </p>
        </div>

        <a href="admin_add_product.php" class="admin-btn">Добавить товар</a>
    </section>

    <section class="admin-kpi-row">
        <div><strong><?= $stats['products'] ?></strong><span>товаров</span></div>
        <div><strong><?= $stats['orders'] ?></strong><span>заказов</span></div>
        <div><strong><?= $stats['newOrders'] ?></strong><span>в работе</span></div>
        <div><strong><?= money($stats['revenue']) ?></strong><span>оборот</span></div>
    </section>

    <section class="admin-grid">
        <a href="admin_products.php" class="admin-panel-card">
            <span class="admin-card-label">Каталог</span>
            <strong><?= $stats['products'] ?></strong>
            <h3>Товары</h3>
            <p>Редактирование, удаление и быстрый переход к карточкам товаров.</p>
        </a>

        <a href="admin_orders.php" class="admin-panel-card">
            <span class="admin-card-label">Продажи</span>
            <strong><?= $stats['orders'] ?></strong>
            <h3>Заказы</h3>
            <p>Контроль статусов заказов и работа с новыми заявками магазина.</p>
        </a>

        <a href="admin_users.php" class="admin-panel-card">
            <span class="admin-card-label">Аккаунты</span>
            <strong><?= $stats['users'] ?></strong>
            <h3>Пользователи</h3>
            <p>Поиск пользователей и управление ролями в административной зоне.</p>
        </a>

        <a href="admin_orders.php?status=new" class="admin-panel-card">
            <span class="admin-card-label">Новые</span>
            <strong><?= $stats['newOrders'] ?></strong>
            <h3>Заказы в работу</h3>
            <p>Отдельный быстрый переход к заказам, которые ещё ждут обработки.</p>
        </a>

        <a href="admin_orders.php" class="admin-panel-card">
            <span class="admin-card-label">Оборот</span>
            <strong><?= money($stats['revenue']) ?></strong>
            <h3>Выручка</h3>
            <p>Сумма заказов без отменённых позиций.</p>
        </a>
    </section>
</div>

</body>
</html>
