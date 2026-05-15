<?php
require 'auth_admin.php';
require 'config/db.php';

$allowedStatuses = [
    'new' => 'Новый',
    'processing' => 'В обработке',
    'done' => 'Выполнен',
];

if (isset($_POST['update_status'])) {
    $id = (int) ($_POST['order_id'] ?? 0);
    $status = (string) ($_POST['status'] ?? 'new');

    if (!isset($allowedStatuses[$status])) {
        $status = 'new';
    }

    $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $id);
    $stmt->execute();

    $queryString = $_SERVER['QUERY_STRING'] ?? '';
    header('Location: admin_orders.php' . ($queryString !== '' ? '?' . $queryString : ''));
    exit();
}

$search = trim($_GET['search'] ?? '');
$statusFilter = trim($_GET['status'] ?? '');
$safeSearch = $conn->real_escape_string($search);
$safeStatus = $conn->real_escape_string($statusFilter);

$sql = "
    SELECT o.*, u.login, u.telephone, p.name AS product_name
    FROM orders o
    JOIN users u ON o.user_id = u.id
    JOIN products p ON o.product_id = p.id
    WHERE 1=1
";

if ($search !== '') {
    $sql .= " AND (u.login LIKE '%$safeSearch%' OR u.telephone LIKE '%$safeSearch%')";
}

if ($statusFilter !== '') {
    $sql .= " AND o.status = '$safeStatus'";
}

$sql .= " ORDER BY o.id DESC";

$result = $conn->query($sql);
$orders = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Заказы</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>

<?php require 'header.php'; ?>

<div class="page-shell admin-page">
    <section class="admin-section">
        <div class="admin-toolbar">
            <div>
                <p class="admin-eyebrow">Продажи</p>
                <h2>Управление заказами</h2>
                <p class="admin-lead">
                    Ищите заказы по логину или телефону клиента и быстро меняйте текущий статус.
                </p>
            </div>
        </div>

        <form method="GET" class="admin-search">
            <input
                type="text"
                name="search"
                value="<?= htmlspecialchars($search, ENT_QUOTES, 'UTF-8') ?>"
                placeholder="Логин или телефон"
            >

            <select name="status">
                <option value="">Все статусы</option>
                <?php foreach ($allowedStatuses as $key => $label): ?>
                    <option value="<?= $key ?>" <?= $statusFilter === $key ? 'selected' : '' ?>>
                        <?= $label ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <button class="btn" type="submit">Найти</button>
        </form>

        <?php if (empty($orders)): ?>
            <div class="admin-empty">
                <h3>Заказы не найдены</h3>
                <p>Попробуйте изменить фильтр или дождитесь новых заказов.</p>
            </div>
        <?php else: ?>
            <div class="admin-list">
                <?php foreach ($orders as $order): ?>
                    <?php $statusKey = (string) $order['status']; ?>
                    <article class="admin-list-card">
                        <div class="admin-list-head">
                            <div>
                                <span class="admin-card-label">Заказ #<?= (int) $order['id'] ?></span>
                                <h3><?= htmlspecialchars($order['product_name'], ENT_QUOTES, 'UTF-8') ?></h3>
                            </div>

                            <span class="status-badge status-<?= htmlspecialchars($statusKey, ENT_QUOTES, 'UTF-8') ?>">
                                <?= htmlspecialchars($allowedStatuses[$statusKey] ?? $statusKey, ENT_QUOTES, 'UTF-8') ?>
                            </span>
                        </div>

                        <div class="admin-list-meta">
                            <p>Пользователь: <?= htmlspecialchars($order['login'], ENT_QUOTES, 'UTF-8') ?></p>
                            <p>Телефон: <?= htmlspecialchars((string) $order['telephone'], ENT_QUOTES, 'UTF-8') ?></p>
                            <p>Количество: <?= (int) $order['quantity'] ?></p>
                            <p>Сумма: <?= (int) $order['total_price'] ?> ₽</p>
                            <p>Дата: <?= htmlspecialchars($order['created_at'], ENT_QUOTES, 'UTF-8') ?></p>
                        </div>

                        <form method="POST" class="admin-inline-form">
                            <input type="hidden" name="order_id" value="<?= (int) $order['id'] ?>">

                            <select name="status">
                                <?php foreach ($allowedStatuses as $key => $label): ?>
                                    <option value="<?= $key ?>" <?= $statusKey === $key ? 'selected' : '' ?>>
                                        <?= $label ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>

                            <button class="btn" type="submit" name="update_status">Сохранить статус</button>
                        </form>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>
</div>

</body>
</html>
