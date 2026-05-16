<?php
session_start();
require 'config/db.php';
require_once 'functions.php';

$categories = fetch_categories($conn);
$category = (int) ($_GET['category'] ?? 0);
$sort = $_GET['sort'] ?? '';
$query = trim($_GET['q'] ?? '');
$minPrice = trim($_GET['min_price'] ?? '');
$maxPrice = trim($_GET['max_price'] ?? '');

$where = [];
$params = [];
$types = '';

if ($category > 0) {
    $where[] = 'p.category_id = ?';
    $params[] = $category;
    $types .= 'i';
}

if ($query !== '') {
    $where[] = '(p.name LIKE ? OR p.short_description LIKE ? OR p.description LIKE ?)';
    $like = '%' . $query . '%';
    $params[] = $like;
    $params[] = $like;
    $params[] = $like;
    $types .= 'sss';
}

if ($minPrice !== '') {
    $where[] = 'p.price >= ?';
    $params[] = (int) $minPrice;
    $types .= 'i';
}

if ($maxPrice !== '') {
    $where[] = 'p.price <= ?';
    $params[] = (int) $maxPrice;
    $types .= 'i';
}

$orderBy = 'ORDER BY p.id DESC';
if ($sort === 'price_asc') {
    $orderBy = 'ORDER BY p.price ASC';
} elseif ($sort === 'price_desc') {
    $orderBy = 'ORDER BY p.price DESC';
} elseif ($sort === 'name_asc') {
    $orderBy = 'ORDER BY p.name ASC';
}

$sql = "
    SELECT p.*, c.name AS category_name
    FROM products p
    LEFT JOIN categories c ON c.id = p.category_id
";

if (!empty($where)) {
    $sql .= ' WHERE ' . implode(' AND ', $where);
}

$sql .= " $orderBy";

$stmt = $conn->prepare($sql);
if ($params) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$products = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Каталог электроники — DАЙКОМ Store</title>
    <link rel="stylesheet" href="css/styles.css">
</head>

<body>

<?php require 'header.php'; ?>

<main class="page-shell catalog-page">
    <section class="catalog-hero">
        <div>
            <div class="hero-kicker">
                <span>Фильтры</span>
                <span>Сортировка</span>
                <span>Реальные категории</span>
            </div>
            <p class="eyebrow">Каталог товаров</p>
            <h1>Каталог, который помогает выбрать быстрее</h1>
            <p>
                Ноутбуки, компьютеры, мониторы, видеокарты, SSD, периферия и кресла.
                Цены и наличие могут отличаться в розничных магазинах, менеджер подтвердит заказ.
            </p>
        </div>
        <div class="catalog-hero-panel">
            <strong><?= count($products) ?></strong>
            <span>товаров найдено</span>
        </div>
    </section>

    <form method="GET" class="catalog-layout">
        <aside class="catalog-filters">
            <div class="filter-head">
                <h2>Фильтры</h2>
                <span><?= count($products) ?> найдено</span>
            </div>

            <label>
                Поиск
                <input type="search" name="q" value="<?= h($query) ?>" placeholder="Например, RTX или Acer">
            </label>

            <label>
                Категория
                <select name="category">
                    <option value="0">Все категории</option>
                    <?php foreach ($categories as $item): ?>
                        <option value="<?= (int) $item['id'] ?>" <?= $category === (int) $item['id'] ? 'selected' : '' ?>>
                            <?= h($item['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </label>

            <div class="filter-row">
                <label>
                    Цена от
                    <input type="number" name="min_price" value="<?= h($minPrice) ?>" min="0" placeholder="0">
                </label>
                <label>
                    до
                    <input type="number" name="max_price" value="<?= h($maxPrice) ?>" min="0" placeholder="150000">
                </label>
            </div>

            <label>
                Сортировка
                <select name="sort">
                    <option value="">Сначала новые</option>
                    <option value="price_asc" <?= $sort === 'price_asc' ? 'selected' : '' ?>>Цена ↑</option>
                    <option value="price_desc" <?= $sort === 'price_desc' ? 'selected' : '' ?>>Цена ↓</option>
                    <option value="name_asc" <?= $sort === 'name_asc' ? 'selected' : '' ?>>Название A-Z</option>
                </select>
            </label>

            <button class="btn btn-primary" type="submit">Применить</button>
            <a class="btn btn-glass" href="products.php">Сбросить</a>
        </aside>

        <section class="products-grid catalog-products">
            <?php if (empty($products)): ?>
                <div class="empty-state">
                    <h2>Ничего не найдено</h2>
                    <p>Попробуйте изменить фильтры или напишите нам — подберём технику под заказ.</p>
                </div>
            <?php endif; ?>

            <?php foreach ($products as $product): ?>
                <article class="product-card">
                    <?php if (!empty($product['badge'])): ?>
                        <span class="product-badge"><?= h($product['badge']) ?></span>
                    <?php endif; ?>

                    <a class="product-media" href="product.php?id=<?= (int) $product['id'] ?>">
                        <img src="<?= product_image($product['image']) ?>" alt="<?= h($product['name']) ?>">
                    </a>

                    <div class="product-body">
                        <span class="product-category"><?= h($product['category_name']) ?></span>
                        <h3><a href="product.php?id=<?= (int) $product['id'] ?>"><?= h($product['name']) ?></a></h3>
                        <p><?= h($product['short_description']) ?></p>
                        <div class="stock-line">
                            <span><?= (int) $product['stock'] > 0 ? 'В наличии: ' . (int) $product['stock'] . ' шт.' : 'Под заказ' ?></span>
                            <span>Код: <?= (int) $product['id'] ?></span>
                        </div>
                        <div class="product-meta">
                            <span>Подтверждение менеджером</span>
                            <span>Сервис DАЙКОМ</span>
                        </div>
                        <div class="product-footer">
                            <strong><?= money($product['price']) ?></strong>
                            <div class="product-actions">
                                <a class="btn btn-small btn-glass" href="product.php?id=<?= (int) $product['id'] ?>">Подробнее</a>
                                <a class="btn btn-small" href="add_to_cart.php?id=<?= (int) $product['id'] ?>">В корзину</a>
                            </div>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        </section>
    </form>
</main>

<?php require 'footer.php'; ?>

</body>
</html>
