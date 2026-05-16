<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$isLoggedIn = !empty($_SESSION['logged_in']);
$isAdmin = $isLoggedIn && (($_SESSION['role'] ?? '') === 'admin');
$roleLabel = htmlspecialchars((string) ($_SESSION['role'] ?? ''), ENT_QUOTES, 'UTF-8');
$searchValue = htmlspecialchars((string) ($_GET['q'] ?? ''), ENT_QUOTES, 'UTF-8');
?>
<header class="site-header">
    <a class="brand" href="index.php" aria-label="Дайком, на главную">
        <span class="brand-mark">D</span>
        <span class="brand-text">
            <strong>Дайком</strong>
            <small>Electro Shop</small>
        </span>
    </a>

    <form class="header-search" method="GET" action="products.php">
        <input type="search" name="q" value="<?= $searchValue ?>" placeholder="Найти технику" aria-label="Поиск товаров">
        <button type="submit">Найти</button>
    </form>

    <nav class="desktop-nav" aria-label="Основная навигация">
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

    <details class="mobile-nav">
        <summary>Меню</summary>
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
    </details>
</header>
