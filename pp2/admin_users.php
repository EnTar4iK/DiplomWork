<?php
require 'auth_admin.php';
require 'config/db.php';
require_once 'functions.php';

$search     = trim((string) ($_GET['search'] ?? ''));
$roleFilter = (string) ($_GET['role'] ?? '');
$sort       = (string) ($_GET['sort'] ?? 'newest');

$conditions = [];
$params = [];
$types = '';

if ($search !== '') {
    $like = '%' . $search . '%';
    $conditions[] = '(login LIKE ? OR telephone LIKE ?)';
    $params[] = $like;
    $params[] = $like;
    $types .= 'ss';
}

if ($roleFilter === 'admin' || $roleFilter === 'user') {
    $conditions[] = 'role = ?';
    $params[] = $roleFilter;
    $types .= 's';
}

$orderBy = 'id DESC';
switch ($sort) {
    case 'oldest':   $orderBy = 'id ASC'; break;
    case 'login_asc':$orderBy = 'login ASC'; break;
    case 'newest':
    default:         $orderBy = 'id DESC'; break;
}

$sql = "SELECT * FROM users";
if (!empty($conditions)) {
    $sql .= ' WHERE ' . implode(' AND ', $conditions);
}
$sql .= ' ORDER BY ' . $orderBy;

$users = [];
if (!empty($params)) {
    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();
    $users = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    $stmt->close();
} else {
    $result = $conn->query($sql);
    $users = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
}

$totalRow = $conn->query("SELECT COUNT(*) AS c FROM users")->fetch_assoc();
$totalUsers = (int) ($totalRow['c'] ?? 0);

$ordersPerUser = [];
$res = $conn->query("SELECT user_id, COUNT(*) AS c FROM orders GROUP BY user_id");
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $ordersPerUser[(int) $row['user_id']] = (int) $row['c'];
    }
}

$adminActive = 'users';
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Пользователи — ДАЙКОМ</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>

<?php require 'header.php'; ?>

<main class="page-shell admin-page">
    <header class="admin-page-head">
        <div>
            <p class="admin-eyebrow">Аккаунты</p>
            <h1>Пользователи</h1>
            <p class="admin-lead">
                Поиск по логину или телефону, фильтрация по роли, изменение прав прямо из таблицы.
            </p>
        </div>
        <div class="admin-page-head-actions">
            <a class="btn btn-secondary" href="admin.php">К дашборду</a>
        </div>
    </header>

    <?php require 'admin_nav.php'; ?>

    <form method="GET" class="admin-filter-bar" role="search">
        <label class="admin-field admin-field-search">
            <span>Поиск</span>
            <input type="search" name="search" value="<?= h($search) ?>" placeholder="Логин или телефон">
        </label>

        <label class="admin-field">
            <span>Роль</span>
            <select name="role">
                <option value="">Все роли</option>
                <option value="user"  <?= $roleFilter === 'user'  ? 'selected' : '' ?>>user</option>
                <option value="admin" <?= $roleFilter === 'admin' ? 'selected' : '' ?>>admin</option>
            </select>
        </label>

        <label class="admin-field">
            <span>Сортировка</span>
            <select name="sort">
                <option value="newest"    <?= $sort === 'newest'    ? 'selected' : '' ?>>Новые сверху</option>
                <option value="oldest"    <?= $sort === 'oldest'    ? 'selected' : '' ?>>Сначала старые</option>
                <option value="login_asc" <?= $sort === 'login_asc' ? 'selected' : '' ?>>По логину</option>
            </select>
        </label>

        <div class="admin-filter-actions">
            <button class="btn btn-primary" type="submit">Применить</button>
            <a class="btn btn-secondary" href="admin_users.php">Сброс</a>
        </div>
    </form>

    <div class="admin-result-meta">
        Найдено: <strong><?= count($users) ?></strong> из <?= $totalUsers ?>
    </div>

    <?php if (empty($users)): ?>
        <div class="admin-empty">
            <h3>Пользователи не найдены</h3>
            <p>Попробуйте уточнить запрос поиска или сбросить фильтр.</p>
        </div>
    <?php else: ?>
        <div class="admin-table-wrap">
            <table class="admin-table admin-table-users">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Логин</th>
                        <th>Телефон</th>
                        <th>Заказов</th>
                        <th>Роль</th>
                        <th>Действия</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user):
                        $uid = (int) $user['id'];
                        $role = (string) $user['role'];
                        $userOrders = $ordersPerUser[$uid] ?? 0;
                    ?>
                        <tr>
                            <td>#<?= $uid ?></td>
                            <td><strong><?= h($user['login']) ?></strong></td>
                            <td><?= h((string) ($user['telephone'] ?? '')) ?: '—' ?></td>
                            <td><?= $userOrders ?></td>
                            <td>
                                <span class="role-badge role-<?= h($role) ?>"><?= h($role) ?></span>
                            </td>
                            <td class="admin-table-actions">
                                <form method="POST" action="update_role.php" class="admin-inline-form">
                                    <input type="hidden" name="id" value="<?= $uid ?>">
                                    <select name="role">
                                        <option value="user"  <?= $role === 'user'  ? 'selected' : '' ?>>user</option>
                                        <option value="admin" <?= $role === 'admin' ? 'selected' : '' ?>>admin</option>
                                    </select>
                                    <button class="btn btn-small btn-primary" type="submit">Сохранить</button>
                                </form>
                                <?php if ($userOrders > 0): ?>
                                    <a class="btn btn-small btn-ghost-link" href="admin_orders.php?search=<?= urlencode($user['login']) ?>">Заказы</a>
                                <?php endif; ?>
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
