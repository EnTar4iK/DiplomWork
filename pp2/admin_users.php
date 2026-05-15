<?php
require 'auth_admin.php';
require 'config/db.php';

$search = trim($_GET['search'] ?? '');
$safeSearch = $conn->real_escape_string($search);

$sql = "SELECT * FROM users WHERE 1=1";

if ($search !== '') {
    $sql .= " AND (login LIKE '%$safeSearch%' OR telephone LIKE '%$safeSearch%')";
}

$sql .= " ORDER BY id DESC";

$result = $conn->query($sql);
$users = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Пользователи</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>

<?php require 'header.php'; ?>

<div class="page-shell admin-page">
    <section class="admin-section">
        <div class="admin-toolbar">
            <div>
                <p class="admin-eyebrow">Пользователи</p>
                <h2>Управление пользователями</h2>
                <p class="admin-lead">
                    Используйте поиск по логину или телефону и меняйте роль прямо из карточки пользователя.
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

            <div></div>

            <button class="btn" type="submit">Найти</button>
        </form>

        <?php if (empty($users)): ?>
            <div class="admin-empty">
                <h3>Пользователи не найдены</h3>
                <p>Попробуйте уточнить запрос поиска.</p>
            </div>
        <?php else: ?>
            <div class="admin-card-grid">
                <?php foreach ($users as $user): ?>
                    <article class="admin-user-card">
                        <span class="admin-card-label">Пользователь #<?= (int) $user['id'] ?></span>
                        <h3><?= htmlspecialchars($user['login'], ENT_QUOTES, 'UTF-8') ?></h3>
                        <p>Телефон: <?= htmlspecialchars((string) $user['telephone'], ENT_QUOTES, 'UTF-8') ?></p>
                        <p>Текущая роль: <?= htmlspecialchars($user['role'], ENT_QUOTES, 'UTF-8') ?></p>

                        <form method="POST" action="update_role.php" class="admin-inline-form">
                            <input type="hidden" name="id" value="<?= (int) $user['id'] ?>">

                            <select name="role">
                                <option value="user" <?= $user['role'] === 'user' ? 'selected' : '' ?>>user</option>
                                <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>admin</option>
                            </select>

                            <button class="btn" type="submit">Сохранить роль</button>
                        </form>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>
</div>

</body>
</html>
