<?php
session_start();
require 'config/db.php';
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Товары</title>
    <link rel="stylesheet" href="css/styles.css">
</head>

<body>

<?php require 'header.php'; ?>

<?php
$sort = $_GET['sort'] ?? '';
$search = trim($_GET['q'] ?? '');
$orderBy = '';

switch ($sort) {
    case 'price_asc':
        $orderBy = "ORDER BY price ASC";
        break;
    case 'price_desc':
        $orderBy = "ORDER BY price DESC";
        break;
    case 'name_asc':
        $orderBy = "ORDER BY name ASC";
        break;
}

$whereClause = '';
$products = [];

if ($search !== '') {
    $whereClause = "WHERE name LIKE ? OR description LIKE ?";
    $likeSearch = '%' . $search . '%';
    $stmt = $conn->prepare("SELECT * FROM products $whereClause $orderBy");
    $stmt->bind_param("ss", $likeSearch, $likeSearch);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = $conn->query("SELECT * FROM products $orderBy");
}

if ($result) {
    $products = $result->fetch_all(MYSQLI_ASSOC);
}
?>

<main class="page-shell catalog-page">
    <section class="catalog-hero">
        <div>
            <p class="eyebrow">Каталог электроники</p>
            <h1>Гаджеты, которые легко выбрать</h1>
            <p>
                Сортируйте товары по цене и названию, открывайте описание и добавляйте
                позиции в корзину без изменения привычного сценария покупки.
            </p>
        </div>
        <div class="catalog-hero-card">
            <span>В каталоге</span>
            <strong><?= count($products) ?></strong>
            <small>товаров найдено</small>
        </div>
    </section>

    <form method="GET" class="catalog-controls">
        <label>
            <span>Поиск</span>
            <input
                type="search"
                name="q"
                value="<?= htmlspecialchars($search, ENT_QUOTES, 'UTF-8') ?>"
                placeholder="iPhone, Samsung, MacBook"
            >
        </label>

        <label>
            <span>Сортировка</span>
            <select name="sort">
                <option value="" <?= $sort === '' ? 'selected' : '' ?>>Без сортировки</option>
                <option value="price_asc" <?= $sort === 'price_asc' ? 'selected' : '' ?>>Цена ↑</option>
                <option value="price_desc" <?= $sort === 'price_desc' ? 'selected' : '' ?>>Цена ↓</option>
                <option value="name_asc" <?= $sort === 'name_asc' ? 'selected' : '' ?>>Имя A-Z</option>
            </select>
        </label>

        <button class="btn btn-primary" type="submit">Показать</button>
    </form>

    <div class="category-chips" aria-label="Популярные категории">
        <a href="products.php?q=iphone">Смартфоны</a>
        <a href="products.php?q=macbook">Ноутбуки</a>
        <a href="products.php?q=samsung">Android</a>
        <a href="products.php">Все товары</a>
    </div>

    <?php if (empty($products)): ?>
        <section class="empty-state">
            <h2>Товары не найдены</h2>
            <p>Попробуйте изменить запрос или сбросить фильтры каталога.</p>
            <a class="btn btn-secondary" href="products.php">Сбросить фильтры</a>
        </section>
    <?php else: ?>
        <section class="products-container" aria-label="Список товаров">
            <?php foreach ($products as $row): ?>
                <article class="product-card">
                    <div class="product-media">
                        <img
                            class="product-img"
                            src="images/<?= rawurlencode($row['image']) ?>"
                            alt="<?= htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8') ?>"
                        >
                    </div>

                    <div class="product-info">
                        <span class="product-category">Электроника</span>
                        <h3><?= htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8') ?></h3>
                        <p class="desc"><?= htmlspecialchars($row['description'], ENT_QUOTES, 'UTF-8') ?></p>

                        <div class="product-footer">
                            <div class="price"><?= number_format((int) $row['price'], 0, ',', ' ') ?> ₽</div>
                            <div class="product-actions">
                                <button class="btn btn-primary" onclick="addToCart(<?= (int) $row['id'] ?>)">В корзину</button>
                                <button
                                    class="btn btn-secondary"
                                    onclick='openModal(<?= json_encode($row['description'], JSON_UNESCAPED_UNICODE | JSON_HEX_APOS | JSON_HEX_QUOT) ?>)'
                                >
                                    Подробнее
                                </button>
                            </div>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        </section>
    <?php endif; ?>
</main>

<div id="modal" class="modal" onclick="closeModal()" aria-hidden="true">
    <div class="modal-content" onclick="event.stopPropagation()">
        <button class="modal-close" type="button" onclick="closeModal()" aria-label="Закрыть описание">×</button>
        <p class="eyebrow">Описание товара</p>
        <p id="modal-text"></p>
    </div>
</div>

<script>
function addToCart(id) {
    window.location.href = "add_to_cart.php?id=" + id;
}

function openModal(text) {
    const modal = document.getElementById("modal");
    modal.style.display = "flex";
    modal.setAttribute("aria-hidden", "false");
    document.getElementById("modal-text").innerText = text;
}

function closeModal() {
    const modal = document.getElementById("modal");
    modal.style.display = "none";
    modal.setAttribute("aria-hidden", "true");
}
</script>

</body>
</html>
