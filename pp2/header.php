<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/functions.php';

$isLoggedIn = !empty($_SESSION['logged_in']);
$isAdmin = $isLoggedIn && (($_SESSION['role'] ?? '') === 'admin');
$searchValue = htmlspecialchars((string) ($_GET['q'] ?? ''), ENT_QUOTES, 'UTF-8');
?>
<?php if ($isAdmin): ?>
<header class="site-header site-header-admin">
    <a class="brand" href="profile.php" aria-label="Личный кабинет администратора">
        <span>
            <strong>ДАЙКОМ</strong>
            <small>Панель администратора</small>
        </span>
    </a>
    <nav class="admin-mini-nav" aria-label="Меню администратора">
        <a class="profile-link" href="admin.php">Админ-панель</a>
        <a class="ghost-link" href="logout.php">Выход</a>
    </nav>
</header>
<?php else: ?>
<header class="site-header">
    <input type="checkbox" id="nav-toggle-state" class="nav-toggle-state" aria-hidden="true">

    <div class="brand-row">
        <a class="brand" href="index.php" aria-label="На главную">
            <span>
                <strong>ДАЙКОМ</strong>
                <small>Электроника · Шахты</small>
            </span>
        </a>

        <label for="nav-toggle-state" class="nav-toggle" aria-label="Открыть меню">
            <span></span>
        </label>
    </div>

    <nav class="main-nav" aria-label="Основная навигация">
        <ul class="nav-list">
            <li><a href="index.php">Главная</a></li>
            <li><a href="products.php">Каталог</a></li>
            <li><a href="delivery.php">Доставка и оплата</a></li>
            <li><a href="contacts.php">Контакты</a></li>

            <?php if ($isLoggedIn): ?>
                <li><a href="orders.php">Заказы</a></li>
            <?php endif; ?>
        </ul>
    </nav>

    <form class="header-search" method="GET" action="products.php" role="search">
        <input type="search" name="q" value="<?= $searchValue ?>" placeholder="Найти товар…" aria-label="Поиск товаров">
        <button type="submit" aria-label="Найти">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <circle cx="11" cy="11" r="7"></circle>
                <path d="M21 21l-4.3-4.3"></path>
            </svg>
        </button>
    </form>

    <div class="header-actions">
        <a class="cart-pill" href="cart.php">Корзина <span><?= cart_count() ?></span></a>

        <?php if ($isLoggedIn): ?>
            <a class="profile-link" href="profile.php">Кабинет</a>
            <a class="ghost-link" href="logout.php">Выход</a>
        <?php else: ?>
            <a class="profile-link" href="auth.php">Войти</a>
            <a class="ghost-link" href="register.php">Регистрация</a>
        <?php endif; ?>
    </div>
</header>
<?php endif; ?>
