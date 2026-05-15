<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$isLoggedIn = !empty($_SESSION['logged_in']);
$isAdmin = $isLoggedIn && (($_SESSION['role'] ?? '') === 'admin');
$roleLabel = htmlspecialchars((string) ($_SESSION['role'] ?? ''), ENT_QUOTES, 'UTF-8');
?>
<header class="site-header">
    <div class="logo">
        <h1><a href="index.php">Дайком</a></h1>
    </div>

    <nav>
        <ul class="nav-list">
            <li><a href="index.php">Главная</a></li>

            <?php if ($isAdmin): ?>
                <li><a href="admin.php" class="admin-btn">Админ панель</a></li>
            <?php else: ?>
                <li><a href="products.php">Продукция</a></li>
                <li><a href="cart.php">Корзина</a></li>

                <?php if ($isLoggedIn): ?>
                    <li><a href="orders.php">Заказы</a></li>
                <?php endif; ?>
            <?php endif; ?>

            <?php if ($isLoggedIn): ?>
                <li><a href="profile.php">Мой кабинет (<?= $roleLabel ?>)</a></li>
                <li><a href="logout.php">Выход</a></li>
            <?php else: ?>
                <li><a href="auth.php">Войти</a></li>
                <li><a href="register.php">Регистрация</a></li>
                <li class="nav-status">Гость</li>
            <?php endif; ?>
        </ul>
    </nav>
</header>
