<?php
// Shared admin sub-navigation. Expects $adminActive to be set to one of:
// 'dashboard' | 'products' | 'orders' | 'users' | 'add'.
// Counts are recomputed cheaply on each request.
if (!isset($adminActive)) {
    $adminActive = '';
}

$adminCounts = [
    'products' => 0,
    'orders' => 0,
    'users' => 0,
];

if (isset($conn) && $conn instanceof mysqli) {
    foreach (['products', 'orders', 'users'] as $table) {
        $res = $conn->query("SELECT COUNT(*) AS c FROM $table");
        if ($res && ($row = $res->fetch_assoc())) {
            $adminCounts[$table] = (int) $row['c'];
        }
    }
}
?>
<nav class="admin-tabs" aria-label="Разделы админ-панели">
    <a href="admin.php" class="<?= $adminActive === 'dashboard' ? 'is-active' : '' ?>">
        <span>Дашборд</span>
    </a>
    <a href="admin_products.php" class="<?= $adminActive === 'products' ? 'is-active' : '' ?>">
        <span>Товары</span>
        <em><?= $adminCounts['products'] ?></em>
    </a>
    <a href="admin_orders.php" class="<?= $adminActive === 'orders' ? 'is-active' : '' ?>">
        <span>Заказы</span>
        <em><?= $adminCounts['orders'] ?></em>
    </a>
    <a href="admin_users.php" class="<?= $adminActive === 'users' ? 'is-active' : '' ?>">
        <span>Пользователи</span>
        <em><?= $adminCounts['users'] ?></em>
    </a>
    <a href="admin_add_product.php" class="admin-tabs-cta <?= $adminActive === 'add' ? 'is-active' : '' ?>">
        + Добавить товар
    </a>
</nav>
