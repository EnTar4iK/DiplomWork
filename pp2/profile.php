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

    <main class="page-shell">
        <section class="profile-card">
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
        </section>
    </main>

    <?php require 'footer.php'; ?>

</body>
</html>
