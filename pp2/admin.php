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
<?php
$revenueMax = !empty($revenueData) ? max($revenueData) : 0;
$ordersMax = !empty($ordersByDayData) ? max($ordersByDayData) : 0;
$statusTotal = array_sum($statusData);
$topMax = !empty($topProductsData) ? max($topProductsData) : 0;
$revenueTotal14 = array_sum($revenueData);
$orders14Total = array_sum($ordersByDayData);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Дашборд — ДАЙКОМ</title>
    <link rel="stylesheet" href="css/styles.css">
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
        <article class="admin-stats-card admin-stats-wide">
            <header>
                <h2>Выручка по дням</h2>
                <p>Последние 14 дней, без отменённых · сумма: <strong><?= money($revenueTotal14) ?></strong></p>
            </header>
            <table class="admin-stats-table admin-stats-table-bars">
                <thead>
                    <tr><th>Дата</th><th>Выручка</th><th>Доля</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($revenueLabels as $i => $label):
                        $value = $revenueData[$i] ?? 0;
                        $pct = $revenueMax > 0 ? round(($value / $revenueMax) * 100) : 0;
                    ?>
                        <tr>
                            <td><?= h($label) ?></td>
                            <td><strong><?= money($value) ?></strong></td>
                            <td>
                                <span class="admin-stats-bar">
                                    <span class="admin-stats-bar-fill" style="width: <?= $pct ?>%"></span>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </article>

        <article class="admin-stats-card">
            <header>
                <h2>Статусы заказов</h2>
                <p>Распределение всех заказов · всего: <strong><?= $statusTotal ?></strong></p>
            </header>
            <?php if (empty($statusLabels)): ?>
                <p class="admin-stats-empty">Заказов пока нет.</p>
            <?php else: ?>
                <ul class="admin-stats-list">
                    <?php foreach ($statusLabels as $i => $label):
                        $value = $statusData[$i] ?? 0;
                        $pct = $statusTotal > 0 ? round(($value / $statusTotal) * 100) : 0;
                    ?>
                        <li>
                            <div class="admin-stats-list-head">
                                <span><?= h($label) ?></span>
                                <strong><?= $value ?> · <?= $pct ?>%</strong>
                            </div>
                            <span class="admin-stats-bar">
                                <span class="admin-stats-bar-fill" style="width: <?= $pct ?>%"></span>
                            </span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </article>
    </section>

    <section class="admin-dashboard-grid">
        <article class="admin-stats-card admin-stats-wide">
            <header>
                <h2>Топ-5 товаров</h2>
                <p>По количеству проданных единиц</p>
            </header>
            <?php if (empty($topProductsRows)): ?>
                <p class="admin-stats-empty">Данных о продажах пока нет.</p>
            <?php else: ?>
                <table class="admin-stats-table">
                    <thead>
                        <tr><th>#</th><th>Товар</th><th>Продано, шт.</th><th>Выручка</th><th>Доля</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($topProductsRows as $i => $row):
                            $qty = (int) $row['qty'];
                            $pct = $topMax > 0 ? round(($qty / $topMax) * 100) : 0;
                        ?>
                            <tr>
                                <td><?= $i + 1 ?></td>
                                <td><strong><?= h($row['product_name']) ?></strong></td>
                                <td><?= $qty ?></td>
                                <td><?= money($row['revenue']) ?></td>
                                <td>
                                    <span class="admin-stats-bar">
                                        <span class="admin-stats-bar-fill" style="width: <?= $pct ?>%"></span>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </article>

        <article class="admin-stats-card">
            <header>
                <h2>Заказы по дням</h2>
                <p>Последние 14 дней · всего: <strong><?= $orders14Total ?></strong></p>
            </header>
            <table class="admin-stats-table admin-stats-table-bars">
                <thead>
                    <tr><th>Дата</th><th>Заказов</th><th>Доля</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($revenueLabels as $i => $label):
                        $value = $ordersByDayData[$i] ?? 0;
                        $pct = $ordersMax > 0 ? round(($value / $ordersMax) * 100) : 0;
                    ?>
                        <tr>
                            <td><?= h($label) ?></td>
                            <td><strong><?= $value ?></strong></td>
                            <td>
                                <span class="admin-stats-bar">
                                    <span class="admin-stats-bar-fill" style="width: <?= $pct ?>%"></span>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
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

</body>
</html>
