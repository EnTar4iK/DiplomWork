<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/functions.php';

$isLoggedIn = !empty($_SESSION['logged_in']);
$isAdmin = $isLoggedIn && (($_SESSION['role'] ?? '') === 'admin');
$roleLabel = htmlspecialchars((string) ($_SESSION['role'] ?? ''), ENT_QUOTES, 'UTF-8');
?>
<header class="site-header">
    <a class="brand" href="index.php" aria-label="На главную">
        <span class="brand-mark">D</span>
        <span>
            <strong>DАЙКОМ Store</strong>
            <small>Электроника · Шахты</small>
        </span>
    </a>

    <nav class="main-nav">
        <ul class="nav-list">
            <li><a href="index.php">Главная</a></li>
            <li><a href="products.php">Каталог</a></li>
            <li><a href="delivery.php">Доставка и оплата</a></li>
            <li><a href="contacts.php">Контакты</a></li>

            <?php if ($isLoggedIn): ?>
                <li><a href="orders.php">Заказы</a></li>
            <?php endif; ?>

            <?php if ($isAdmin): ?>
                <li><a href="admin.php">Админ-панель</a></li>
            <?php endif; ?>
        </ul>
    </nav>

    <div class="header-actions">
        <a class="cart-pill" href="cart.php">Корзина <span><?= cart_count() ?></span></a>

        <?php if ($isLoggedIn): ?>
            <a class="profile-link" href="profile.php">Кабинет <?= $roleLabel !== '' ? '(' . $roleLabel . ')' : '' ?></a>
            <a class="ghost-link" href="logout.php">Выход</a>
        <?php else: ?>
            <a class="profile-link" href="auth.php">Войти</a>
            <a class="ghost-link" href="register.php">Регистрация</a>
        <?php endif; ?>
    </div>
</header>
