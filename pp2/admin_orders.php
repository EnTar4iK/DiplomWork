<?php
require 'auth_admin.php';
require 'config/db.php';
require_once 'functions.php';

$allowedStatuses = order_statuses();
$deliveryMethods = delivery_methods();
$paymentMethods  = payment_methods();

if (isset($_POST['update_status'])) {
    $id = (int) ($_POST['order_id'] ?? 0);
    $status = (string) ($_POST['status'] ?? 'new');

    if (!isset($allowedStatuses[$status])) {
        $status = 'new';
    }

    $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
    $stmt->bind_param('si', $status, $id);
    $stmt->execute();
    $stmt->close();

    $queryString = $_SERVER['QUERY_STRING'] ?? '';
    header('Location: admin_orders.php' . ($queryString !== '' ? '?' . $queryString : ''));
    exit();
}

$search       = trim((string) ($_GET['search'] ?? ''));
$statusFilter = (string) ($_GET['status'] ?? '');
$paymentFilter= (string) ($_GET['payment'] ?? '');
$deliveryFilter = (string) ($_GET['delivery'] ?? '');
$dateFrom     = trim((string) ($_GET['date_from'] ?? ''));
$dateTo       = trim((string) ($_GET['date_to'] ?? ''));
$totalMin     = trim((string) ($_GET['total_min'] ?? ''));
$totalMax     = trim((string) ($_GET['total_max'] ?? ''));
$sort         = (string) ($_GET['sort'] ?? 'newest');

$conditions = [];
$params = [];
$types = '';

if ($search !== '') {
    $like = '%' . $search . '%';
    $conditions[] = '(u.login LIKE ? OR u.telephone LIKE ? OR o.phone LIKE ? OR o.customer_name LIKE ? OR o.email LIKE ?)';
    $params[] = $like; $params[] = $like; $params[] = $like; $params[] = $like; $params[] = $like;
    $types .= 'sssss';
}

if ($statusFilter !== '' && isset($allowedStatuses[$statusFilter])) {
    $conditions[] = 'o.status = ?';
    $params[] = $statusFilter;
    $types .= 's';
}

if ($paymentFilter !== '' && isset($paymentMethods[$paymentFilter])) {
    $conditions[] = 'o.payment_method = ?';
    $params[] = $paymentFilter;
    $types .= 's';
}

if ($deliveryFilter !== '' && isset($deliveryMethods[$deliveryFilter])) {
    $conditions[] = 'o.delivery_method = ?';
    $params[] = $deliveryFilter;
    $types .= 's';
}

if ($dateFrom !== '' && preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateFrom)) {
    $conditions[] = 'DATE(o.created_at) >= ?';
    $params[] = $dateFrom;
    $types .= 's';
}

if ($dateTo !== '' && preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateTo)) {
    $conditions[] = 'DATE(o.created_at) <= ?';
    $params[] = $dateTo;
    $types .= 's';
}

if ($totalMin !== '' && is_numeric($totalMin)) {
    $conditions[] = 'o.total_price >= ?';
    $params[] = (int) $totalMin;
    $types .= 'i';
}

if ($totalMax !== '' && is_numeric($totalMax)) {
    $conditions[] = 'o.total_price <= ?';
    $params[] = (int) $totalMax;
    $types .= 'i';
}

$orderBy = 'o.id DESC';
switch ($sort) {
    case 'oldest':     $orderBy = 'o.id ASC'; break;
    case 'total_desc': $orderBy = 'o.total_price DESC, o.id DESC'; break;
    case 'total_asc':  $orderBy = 'o.total_price ASC, o.id DESC'; break;
    case 'newest':
    default:           $orderBy = 'o.id DESC'; break;
}

$sql = "
    SELECT o.*, u.login, u.telephone
    FROM orders o
    JOIN users u ON o.user_id = u.id
";
if (!empty($conditions)) {
    $sql .= ' WHERE ' . implode(' AND ', $conditions);
}
$sql .= ' ORDER BY ' . $orderBy;

$orders = [];
if (!empty($params)) {
    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();
    $orders = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    $stmt->close();
} else {
    $result = $conn->query($sql);
    $orders = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
}

$totalRow = $conn->query("SELECT COUNT(*) AS c FROM orders")->fetch_assoc();
$totalOrders = (int) ($totalRow['c'] ?? 0);

$filteredRevenue = 0;
foreach ($orders as $o) {
    if (($o['status'] ?? '') !== 'cancelled') {
        $filteredRevenue += (int) $o['total_price'];
    }
}

$adminActive = 'orders';
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Заказы — ДАЙКОМ</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>

<?php require 'header.php'; ?>

<main class="page-shell admin-page">
    <header class="admin-page-head">
        <div>
            <p class="admin-eyebrow">Продажи</p>
            <h1>Заказы</h1>
            <p class="admin-lead">
                Фильтрация по статусу, оплате, доставке и периоду. Изменяйте статус и удаляйте заказы прямо из таблицы.
            </p>
        </div>
        <div class="admin-page-head-actions">
            <a class="btn btn-secondary" href="admin.php">К дашборду</a>
        </div>
    </header>

    <?php require 'admin_nav.php'; ?>

    <form method="GET" class="admin-filter-bar admin-filter-bar-wide" role="search">
        <label class="admin-field admin-field-search">
            <span>Поиск</span>
            <input type="search" name="search" value="<?= h($search) ?>" placeholder="Имя, логин, телефон, e-mail">
        </label>

        <label class="admin-field">
            <span>Статус</span>
            <select name="status">
                <option value="">Все</option>
                <?php foreach ($allowedStatuses as $key => $label): ?>
                    <option value="<?= h($key) ?>" <?= $statusFilter === $key ? 'selected' : '' ?>><?= h($label) ?></option>
                <?php endforeach; ?>
            </select>
        </label>

        <label class="admin-field">
            <span>Оплата</span>
            <select name="payment">
                <option value="">Все</option>
                <?php foreach ($paymentMethods as $key => $label): ?>
                    <option value="<?= h($key) ?>" <?= $paymentFilter === $key ? 'selected' : '' ?>><?= h($label) ?></option>
                <?php endforeach; ?>
            </select>
        </label>

        <label class="admin-field">
            <span>Доставка</span>
            <select name="delivery">
                <option value="">Все</option>
                <?php foreach ($deliveryMethods as $key => $label): ?>
                    <option value="<?= h($key) ?>" <?= $deliveryFilter === $key ? 'selected' : '' ?>><?= h($label) ?></option>
                <?php endforeach; ?>
            </select>
        </label>

        <label class="admin-field">
            <span>Дата с</span>
            <input type="date" name="date_from" value="<?= h($dateFrom) ?>">
        </label>

        <label class="admin-field">
            <span>Дата по</span>
            <input type="date" name="date_to" value="<?= h($dateTo) ?>">
        </label>

        <label class="admin-field">
            <span>Сумма от</span>
            <input type="number" min="0" step="1" name="total_min" value="<?= h($totalMin) ?>" placeholder="0">
        </label>

        <label class="admin-field">
            <span>Сумма до</span>
            <input type="number" min="0" step="1" name="total_max" value="<?= h($totalMax) ?>" placeholder="∞">
        </label>

        <label class="admin-field">
            <span>Сортировка</span>
            <select name="sort">
                <option value="newest"     <?= $sort === 'newest'     ? 'selected' : '' ?>>Новые сверху</option>
                <option value="oldest"     <?= $sort === 'oldest'     ? 'selected' : '' ?>>Сначала старые</option>
                <option value="total_desc" <?= $sort === 'total_desc' ? 'selected' : '' ?>>Сумма ↓</option>
                <option value="total_asc"  <?= $sort === 'total_asc'  ? 'selected' : '' ?>>Сумма ↑</option>
            </select>
        </label>

        <div class="admin-filter-actions">
            <button class="btn btn-primary" type="submit">Применить</button>
            <a class="btn btn-secondary" href="admin_orders.php">Сброс</a>
        </div>
    </form>

    <div class="admin-result-meta">
        Найдено: <strong><?= count($orders) ?></strong> из <?= $totalOrders ?> · Выручка по выборке: <strong><?= money($filteredRevenue) ?></strong>
    </div>

    <?php if (empty($orders)): ?>
        <div class="admin-empty">
            <h3>Заказы не найдены</h3>
            <p>Попробуйте изменить фильтры или сбросить их.</p>
        </div>
    <?php else: ?>
        <div class="admin-table-wrap">
            <table class="admin-table admin-table-orders">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Дата</th>
                        <th>Клиент</th>
                        <th>Контакты</th>
                        <th>Доставка</th>
                        <th>Оплата</th>
                        <th>Сумма</th>
                        <th>Статус</th>
                        <th>Действия</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order):
                        $orderId = (int) $order['id'];
                        $statusKey = (string) $order['status'];
                        $statusLabel = $allowedStatuses[$statusKey] ?? $statusKey;
                        $paymentLabel = $paymentMethods[$order['payment_method']] ?? $order['payment_method'];
                        $deliveryLabel = $deliveryMethods[$order['delivery_method']] ?? $order['delivery_method'];

                        $itemsResult = $conn->query("SELECT product_name, quantity, total_price FROM order_items WHERE order_id = $orderId");
                        $items = $itemsResult ? $itemsResult->fetch_all(MYSQLI_ASSOC) : [];

                        $queryString = http_build_query($_GET);
                    ?>
                        <tr>
                            <td>#<?= $orderId ?></td>
                            <td>
                                <?= h(date('d.m.Y', strtotime((string) $order['created_at']))) ?>
                                <small class="admin-table-sub"><?= h(date('H:i', strtotime((string) $order['created_at']))) ?></small>
                            </td>
                            <td>
                                <strong><?= h($order['customer_name']) ?></strong>
                                <small class="admin-table-sub">логин: <?= h($order['login']) ?></small>
                            </td>
                            <td>
                                <?= h($order['phone'] ?: $order['telephone']) ?>
                                <?php if (!empty($order['email'])): ?>
                                    <small class="admin-table-sub"><?= h($order['email']) ?></small>
                                <?php endif; ?>
                            </td>
                            <td><?= h($deliveryLabel) ?></td>
                            <td><?= h($paymentLabel) ?></td>
                            <td>
                                <strong><?= money($order['total_price']) ?></strong>
                                <small class="admin-table-sub"><?= count($items) ?> поз.</small>
                            </td>
                            <td>
                                <form method="POST" class="admin-inline-form">
                                    <?php foreach ($_GET as $key => $value): ?>
                                        <input type="hidden" name="<?= h($key) ?>" value="<?= h(is_array($value) ? '' : $value) ?>">
                                    <?php endforeach; ?>
                                    <input type="hidden" name="order_id" value="<?= $orderId ?>">
                                    <select name="status">
                                        <?php foreach ($allowedStatuses as $key => $label): ?>
                                            <option value="<?= h($key) ?>" <?= $statusKey === $key ? 'selected' : '' ?>><?= h($label) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <button class="btn btn-small btn-primary" type="submit" name="update_status">Сохранить</button>
                                </form>
                                <span class="status-badge status-<?= h($statusKey) ?>"><?= h($statusLabel) ?></span>
                            </td>
                            <td class="admin-table-actions">
                                <details class="admin-row-details">
                                    <summary>Состав</summary>
                                    <div>
                                        <?php foreach ($items as $item): ?>
                                            <p><?= h($item['product_name']) ?> × <?= (int) $item['quantity'] ?> — <strong><?= money($item['total_price']) ?></strong></p>
                                        <?php endforeach; ?>
                                        <?php if (!empty($order['delivery_address'])): ?>
                                            <p><em>Адрес:</em> <?= h($order['delivery_address']) ?></p>
                                        <?php endif; ?>
                                        <?php if (!empty($order['comment'])): ?>
                                            <p><em>Комментарий:</em> <?= h($order['comment']) ?></p>
                                        <?php endif; ?>
                                    </div>
                                </details>
                                <a
                                    class="btn btn-small btn-danger"
                                    href="delete_order.php?id=<?= $orderId ?>&amp;<?= h($queryString) ?>"
                                    onclick="return confirm('Удалить заказ #<?= $orderId ?>? Действие необратимо.')"
                                >Удалить</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</main>

<?php require 'footer.php'; ?>

</body>
</html>
