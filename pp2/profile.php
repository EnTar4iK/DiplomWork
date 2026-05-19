<?php 
    session_start();
    require 'config/db.php'; 
    require_once 'functions.php';

    require_login();
    $user = current_user($conn);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/styles.css">
    <title>Личный кабинет — ДАЙКОМ</title>
</head>
<body>
    <?php require 'header.php';?>

    <?php $isAdmin = (($_SESSION['role'] ?? '') === 'admin'); ?>

    <main class="page-shell">
        <section class="profile-card<?= $isAdmin ? ' profile-card-admin' : '' ?>">
            <?php if ($isAdmin): ?>
                <div>
                    <p class="eyebrow">Кабинет администратора</p>
                    <h1><?= h($user['login'] ?? 'Администратор') ?></h1>
                </div>

                <div class="profile-admin-warning" role="status">
                    <strong>Внимание!</strong>
                    Вы авторизованы как <strong>Админ</strong>.
                    Клиентские функции — каталог, корзина, оформление заказа и история покупок — для вас недоступны.
                    Используйте панель управления для работы с товарами, заказами и пользователями.
                </div>

                <div class="profile-grid profile-grid-admin">
                    <article>
                        <span>Роль</span>
                        <strong>Администратор</strong>
                    </article>
                    <article>
                        <span>Логин</span>
                        <strong><?= h($user['login'] ?? '—') ?></strong>
                    </article>
                </div>

                <div class="button-row">
                    <a class="btn btn-primary" href="admin.php">Перейти в админ-панель</a>
                    <a class="btn btn-glass" href="logout.php">Выйти</a>
                </div>
            <?php else: ?>
                <div>
                    <p class="eyebrow">Личный кабинет</p>
                    <h1><?= h($user['login'] ?? 'Пользователь') ?></h1>
                    <p>Здесь хранятся контактные данные, история заказов и быстрые действия покупателя.</p>
                </div>

                <div class="profile-grid">
                    <article>
                        <span>Роль</span>
                        <strong><?= h($_SESSION['role'] ?? 'user') ?></strong>
                    </article>
                    <article>
                        <span>Телефон</span>
                        <strong><?= h($user['telephone'] ?? 'Не указан') ?></strong>
                    </article>
                    <article>
                        <span>Статус</span>
                        <strong>Активный клиент</strong>
                    </article>
                </div>

                <div class="button-row">
                    <a class="btn btn-primary" href="orders.php">Мои заказы</a>
                    <a class="btn btn-glass" href="products.php">Продолжить покупки</a>
                </div>
            <?php endif; ?>
        </section>
    </main>

    <?php require 'footer.php'; ?>

</body>
</html>
