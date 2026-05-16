<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/functions.php';

$isLoggedIn = !empty($_SESSION['logged_in']);
$isAdmin = $isLoggedIn && (($_SESSION['role'] ?? '') === 'admin');
$roleLabel = htmlspecialchars((string) ($_SESSION['role'] ?? ''), ENT_QUOTES, 'UTF-8');
$searchValue = htmlspecialchars((string) ($_GET['q'] ?? ''), ENT_QUOTES, 'UTF-8');
?>
<header class="site-header" data-site-header>
    <div class="brand-row">
        <a class="brand" href="index.php" aria-label="На главную">
            <span class="brand-mark">D</span>
            <span>
                <strong>DАЙКОМ Store</strong>
                <small>Электроника · Шахты</small>
            </span>
        </a>

        <button class="nav-toggle" type="button" aria-label="Открыть меню" aria-expanded="false" data-nav-toggle>
            <span></span>
        </button>
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

            <?php if ($isAdmin): ?>
                <li><a href="admin.php">Админ-панель</a></li>
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
            <a class="profile-link" href="profile.php">Кабинет <?= $roleLabel !== '' ? '(' . $roleLabel . ')' : '' ?></a>
            <a class="ghost-link" href="logout.php">Выход</a>
        <?php else: ?>
            <a class="profile-link" href="auth.php">Войти</a>
            <a class="ghost-link" href="register.php">Регистрация</a>
        <?php endif; ?>
    </div>
</header>

<script>
(function () {
    var toggle = document.querySelector('[data-nav-toggle]');
    var header = document.querySelector('[data-site-header]');
    if (!toggle || !header) return;
    toggle.addEventListener('click', function () {
        var open = header.classList.toggle('is-open');
        toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
        toggle.setAttribute('aria-label', open ? 'Закрыть меню' : 'Открыть меню');
    });
})();
</script>
