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
    'products'      => fetchTotal($conn, "SELECT COUNT(*) AS total FROM products"),
    'orders'        => fetchTotal($conn, "SELECT COUNT(*) AS total FROM orders"),
    'users'         => fetchTotal($conn, "SELECT COUNT(*) AS total FROM users"),
    'newOrders'     => fetchTotal($conn, "SELECT COUNT(*) AS total FROM orders WHERE status IN ('new', 'paid')"),
    'doneOrders'    => fetchTotal($conn, "SELECT COUNT(*) AS total FROM orders WHERE status = 'done'"),
    'revenue'       => fetchTotal($conn, "SELECT COALESCE(SUM(total_price), 0) AS total FROM orders WHERE status <> 'cancelled'"),
    'lowStock'      => fetchTotal($conn, "SELECT COUNT(*) AS total FROM products WHERE stock < 3"),
];

$paidCount = max(1, fetchTotal($conn, "SELECT COUNT(*) AS total FROM orders WHERE status <> 'cancelled'"));
$avgCheck = (int) round($stats['revenue'] / $paidCount);

// Revenue by day for the last 14 days, with zero-fill for missing days.
$revenueByDayRaw = [];
$res = $conn->query("
    SELECT DATE(created_at) AS d, COALESCE(SUM(total_price), 0) AS s, COUNT(*) AS c
    FROM orders
    WHERE status <> 'cancelled' AND created_at >= (CURRENT_DATE - INTERVAL 13 DAY)
    GROUP BY DATE(created_at)
");
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $revenueByDayRaw[$row['d']] = ['s' => (int) $row['s'], 'c' => (int) $row['c']];
    }
}

$revenueLabels = [];
$revenueData = [];
$ordersByDayData = [];
for ($i = 13; $i >= 0; $i--) {
    $day = date('Y-m-d', strtotime("-$i day"));
    $label = date('d.m', strtotime($day));
    $revenueLabels[] = $label;
    $revenueData[] = $revenueByDayRaw[$day]['s'] ?? 0;
    $ordersByDayData[] = $revenueByDayRaw[$day]['c'] ?? 0;
}

// Status distribution across all orders.
$statusLabelMap = order_statuses();
$statusRows = [];
$res = $conn->query("SELECT status, COUNT(*) AS c FROM orders GROUP BY status");
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $statusRows[(string) $row['status']] = (int) $row['c'];
    }
}
$statusLabels = [];
$statusData = [];
foreach ($statusLabelMap as $key => $label) {
    if (!empty($statusRows[$key])) {
        $statusLabels[] = $label;
        $statusData[] = $statusRows[$key];
    }
}

// Top 5 products by sold quantity, excluding cancelled orders.
$topProductsLabels = [];
$topProductsData = [];
$topProductsRows = [];
$res = $conn->query("
    SELECT product_name, SUM(quantity) AS qty, SUM(oi.total_price) AS revenue
    FROM order_items oi
    JOIN orders o ON o.id = oi.order_id
    WHERE o.status <> 'cancelled'
    GROUP BY product_name
    ORDER BY qty DESC
    LIMIT 5
");
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $topProductsRows[] = $row;
        $topProductsLabels[] = $row['product_name'];
        $topProductsData[] = (int) $row['qty'];
    }
}

// Recent 10 orders for the table.
$recentOrders = [];
$res = $conn->query("
    SELECT o.*, u.login
    FROM orders o
    JOIN users u ON u.id = o.user_id
    ORDER BY o.id DESC
    LIMIT 10
");
if ($res) {
    $recentOrders = $res->fetch_all(MYSQLI_ASSOC);
}

$paymentMethods = payment_methods();
$adminActive = 'dashboard';
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Дашборд — ДАЙКОМ</title>
    <link rel="stylesheet" href="css/styles.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js" defer></script>
</head>
<body>

<?php require 'header.php'; ?>

<main class="page-shell admin-page">
    <header class="admin-page-head">
        <div>
            <p class="admin-eyebrow">Панель управления</p>
            <h1>Дашборд</h1>
            <p class="admin-lead">Обзор продаж, заказов и каталога за последние 14 дней.</p>
        </div>
        <div class="admin-page-head-actions">
            <a class="btn btn-secondary" href="admin_orders.php?status=new">Заказы в работе <em><?= $stats['newOrders'] ?></em></a>
            <a class="btn btn-primary" href="admin_add_product.php">+ Добавить товар</a>
        </div>
    </header>

    <?php require 'admin_nav.php'; ?>

    <section class="admin-kpi-strip" aria-label="Ключевые показатели">
        <div class="admin-kpi">
            <span class="admin-kpi-label">Выручка</span>
            <strong class="admin-kpi-value"><?= money($stats['revenue']) ?></strong>
            <span class="admin-kpi-hint">без отменённых</span>
        </div>
        <div class="admin-kpi">
            <span class="admin-kpi-label">Заказов</span>
            <strong class="admin-kpi-value"><?= $stats['orders'] ?></strong>
            <span class="admin-kpi-hint"><?= $stats['newOrders'] ?> в работе · <?= $stats['doneOrders'] ?> выполнено</span>
        </div>
        <div class="admin-kpi">
            <span class="admin-kpi-label">Средний чек</span>
            <strong class="admin-kpi-value"><?= money($avgCheck) ?></strong>
            <span class="admin-kpi-hint">по активным заказам</span>
        </div>
        <div class="admin-kpi">
            <span class="admin-kpi-label">Товаров</span>
            <strong class="admin-kpi-value"><?= $stats['products'] ?></strong>
            <span class="admin-kpi-hint"><?= $stats['lowStock'] ?> на исходе</span>
        </div>
        <div class="admin-kpi">
            <span class="admin-kpi-label">Пользователей</span>
            <strong class="admin-kpi-value"><?= $stats['users'] ?></strong>
            <span class="admin-kpi-hint">всего аккаунтов</span>
        </div>
    </section>

    <section class="admin-dashboard-grid">
        <article class="admin-chart-card admin-chart-wide">
            <header>
                <h2>Выручка по дням</h2>
                <p>Последние 14 дней, заказы без отменённых</p>
            </header>
            <div class="admin-chart-canvas-wrap">
                <canvas id="revenueChart" aria-label="График выручки по дням" role="img"></canvas>
            </div>
        </article>

        <article class="admin-chart-card">
            <header>
                <h2>Статусы заказов</h2>
                <p>Распределение всех заказов</p>
            </header>
            <div class="admin-chart-canvas-wrap">
                <canvas id="statusChart" aria-label="Распределение заказов по статусам" role="img"></canvas>
            </div>
        </article>
    </section>

    <section class="admin-dashboard-grid">
        <article class="admin-chart-card admin-chart-wide">
            <header>
                <h2>Топ-5 товаров</h2>
                <p>По количеству проданных единиц</p>
            </header>
            <div class="admin-chart-canvas-wrap">
                <canvas id="topProductsChart" aria-label="Топ товаров по продажам" role="img"></canvas>
            </div>
            <?php if (empty($topProductsRows)): ?>
                <p class="admin-chart-empty">Данных о продажах пока нет.</p>
            <?php endif; ?>
        </article>

        <article class="admin-chart-card">
            <header>
                <h2>Заказы по дням</h2>
                <p>Количество за 14 дней</p>
            </header>
            <div class="admin-chart-canvas-wrap">
                <canvas id="ordersChart" aria-label="Количество заказов по дням" role="img"></canvas>
            </div>
        </article>
    </section>

    <section class="admin-section admin-recent-orders">
        <header class="admin-section-head">
            <div>
                <p class="admin-eyebrow">Последние заказы</p>
                <h2>Свежие операции</h2>
            </div>
            <a class="btn btn-secondary" href="admin_orders.php">Все заказы</a>
        </header>

        <?php if (empty($recentOrders)): ?>
            <div class="admin-empty">
                <h3>Заказов пока нет</h3>
                <p>Когда поступят новые заявки — они появятся здесь.</p>
            </div>
        <?php else: ?>
            <div class="admin-table-wrap">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Дата</th>
                            <th>Клиент</th>
                            <th>Логин</th>
                            <th>Оплата</th>
                            <th>Сумма</th>
                            <th>Статус</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentOrders as $order):
                            $statusKey = (string) $order['status'];
                            $statusLabel = $statusLabelMap[$statusKey] ?? $statusKey;
                            $payment = $paymentMethods[$order['payment_method']] ?? $order['payment_method'];
                        ?>
                            <tr>
                                <td>#<?= (int) $order['id'] ?></td>
                                <td><?= h(date('d.m.Y H:i', strtotime((string) $order['created_at']))) ?></td>
                                <td><?= h($order['customer_name']) ?></td>
                                <td><?= h($order['login']) ?></td>
                                <td><?= h($payment) ?></td>
                                <td><strong><?= money($order['total_price']) ?></strong></td>
                                <td>
                                    <span class="status-badge status-<?= h($statusKey) ?>"><?= h($statusLabel) ?></span>
                                </td>
                                <td class="admin-table-actions">
                                    <a class="admin-link" href="admin_orders.php?search=<?= urlencode($order['login']) ?>">Открыть</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </section>
</main>

<?php require 'footer.php'; ?>

<script>
window.adminDashboard = {
    revenue: {
        labels: <?= json_encode($revenueLabels, JSON_UNESCAPED_UNICODE) ?>,
        data: <?= json_encode($revenueData) ?>
    },
    ordersByDay: {
        labels: <?= json_encode($revenueLabels, JSON_UNESCAPED_UNICODE) ?>,
        data: <?= json_encode($ordersByDayData) ?>
    },
    status: {
        labels: <?= json_encode($statusLabels, JSON_UNESCAPED_UNICODE) ?>,
        data: <?= json_encode($statusData) ?>
    },
    topProducts: {
        labels: <?= json_encode($topProductsLabels, JSON_UNESCAPED_UNICODE) ?>,
        data: <?= json_encode($topProductsData) ?>
    }
};
</script>
<script src="js/admin-charts.js" defer></script>

</body>
</html>
