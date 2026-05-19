<?php
require 'auth_admin.php';
require 'config/db.php';
require_once 'functions.php';

$categories = fetch_categories($conn);

$search        = trim((string) ($_GET['search'] ?? ''));
$categoryId    = (int) ($_GET['category'] ?? 0);
$priceMin      = trim((string) ($_GET['price_min'] ?? ''));
$priceMax      = trim((string) ($_GET['price_max'] ?? ''));
$stockFilter   = (string) ($_GET['stock'] ?? '');
$sort          = (string) ($_GET['sort'] ?? 'newest');

$priceMinValue = $priceMin === '' ? null : (int) $priceMin;
$priceMaxValue = $priceMax === '' ? null : (int) $priceMax;

$conditions = [];
$params = [];
$types = '';

if ($search !== '') {
    $conditions[] = '(p.name LIKE ? OR p.short_description LIKE ? OR p.description LIKE ?)';
    $like = '%' . $search . '%';
    $params[] = $like;
    $params[] = $like;
    $params[] = $like;
    $types .= 'sss';
}

if ($categoryId > 0) {
    $conditions[] = 'p.category_id = ?';
    $params[] = $categoryId;
    $types .= 'i';
}

if ($priceMinValue !== null) {
    $conditions[] = 'p.price >= ?';
    $params[] = $priceMinValue;
    $types .= 'i';
}

if ($priceMaxValue !== null) {
    $conditions[] = 'p.price <= ?';
    $params[] = $priceMaxValue;
    $types .= 'i';
}

if ($stockFilter === 'in') {
    $conditions[] = 'p.stock > 0';
} elseif ($stockFilter === 'low') {
    $conditions[] = 'p.stock > 0 AND p.stock < 3';
} elseif ($stockFilter === 'out') {
    $conditions[] = 'p.stock = 0';
}

$orderBy = 'p.id DESC';
switch ($sort) {
    case 'name_asc':   $orderBy = 'p.name ASC'; break;
    case 'price_asc':  $orderBy = 'p.price ASC'; break;
    case 'price_desc': $orderBy = 'p.price DESC'; break;
    case 'stock_asc':  $orderBy = 'p.stock ASC, p.id DESC'; break;
    case 'newest':
    default:           $orderBy = 'p.id DESC'; break;
}

$sql = "
    SELECT p.*, c.name AS category_name
    FROM products p
    LEFT JOIN categories c ON c.id = p.category_id
";

if (!empty($conditions)) {
    $sql .= ' WHERE ' . implode(' AND ', $conditions);
}

$sql .= ' ORDER BY ' . $orderBy;

$products = [];
if (!empty($params)) {
    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();
    $products = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    $stmt->close();
} else {
    $result = $conn->query($sql);
    $products = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
}

$totalRow = $conn->query("SELECT COUNT(*) AS c FROM products")->fetch_assoc();
$totalProducts = (int) ($totalRow['c'] ?? 0);

$adminActive = 'products';
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Товары — ДАЙКОМ</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>

<?php require 'header.php'; ?>

<main class="page-shell admin-page">
    <header class="admin-page-head">
        <div>
            <p class="admin-eyebrow">Каталог</p>
            <h1>Товары</h1>
            <p class="admin-lead">
                Управление карточками: фильтрация, поиск, изменение цен и остатков, редактирование и удаление.
            </p>
        </div>
        <div class="admin-page-head-actions">
            <a class="btn btn-primary" href="admin_add_product.php">+ Добавить товар</a>
        </div>
    </header>

    <?php require 'admin_nav.php'; ?>

    <form method="GET" class="admin-filter-bar" role="search">
        <label class="admin-field admin-field-search">
            <span>Поиск</span>
            <input
                type="search"
                name="search"
                value="<?= h($search) ?>"
                placeholder="Название, описание…"
            >
        </label>

        <label class="admin-field">
            <span>Категория</span>
            <select name="category">
                <option value="0">Все категории</option>
                <?php foreach ($categories as $category): ?>
                    <option value="<?= (int) $category['id'] ?>" <?= $categoryId === (int) $category['id'] ? 'selected' : '' ?>>
                        <?= h($category['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label>

        <label class="admin-field">
            <span>Цена от</span>
            <input type="number" min="0" step="1" name="price_min" value="<?= h($priceMin) ?>" placeholder="0">
        </label>

        <label class="admin-field">
            <span>Цена до</span>
            <input type="number" min="0" step="1" name="price_max" value="<?= h($priceMax) ?>" placeholder="∞">
        </label>

        <label class="admin-field">
            <span>Остаток</span>
            <select name="stock">
                <option value="">Любой</option>
                <option value="in" <?= $stockFilter === 'in' ? 'selected' : '' ?>>В наличии</option>
                <option value="low" <?= $stockFilter === 'low' ? 'selected' : '' ?>>На исходе (&lt;3)</option>
                <option value="out" <?= $stockFilter === 'out' ? 'selected' : '' ?>>Закончились</option>
            </select>
        </label>

        <label class="admin-field">
            <span>Сортировка</span>
            <select name="sort">
                <option value="newest"    <?= $sort === 'newest'    ? 'selected' : '' ?>>Новые сверху</option>
                <option value="name_asc"  <?= $sort === 'name_asc'  ? 'selected' : '' ?>>По названию</option>
                <option value="price_asc" <?= $sort === 'price_asc' ? 'selected' : '' ?>>Цена ↑</option>
                <option value="price_desc"<?= $sort === 'price_desc'? 'selected' : '' ?>>Цена ↓</option>
                <option value="stock_asc" <?= $sort === 'stock_asc' ? 'selected' : '' ?>>Остаток ↑</option>
            </select>
        </label>

        <div class="admin-filter-actions">
            <button class="btn btn-primary" type="submit">Применить</button>
            <a class="btn btn-secondary" href="admin_products.php">Сброс</a>
        </div>
    </form>

    <div class="admin-result-meta">
        Найдено: <strong><?= count($products) ?></strong> из <?= $totalProducts ?>
    </div>

    <?php if (empty($products)): ?>
        <div class="admin-empty">
            <h3>Ничего не найдено</h3>
            <p>Попробуйте изменить фильтры или сбросить их.</p>
        </div>
    <?php else: ?>
        <div class="admin-table-wrap">
            <table class="admin-table admin-table-products">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Фото</th>
                        <th>Название</th>
                        <th>Категория</th>
                        <th>Цена</th>
                        <th>Остаток</th>
                        <th>Бейдж</th>
                        <th>Действия</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $product):
                        $pid = (int) $product['id'];
                        $stock = (int) $product['stock'];
                        $stockClass = $stock === 0 ? 'is-out' : ($stock < 3 ? 'is-low' : 'is-ok');
                    ?>
                        <tr>
                            <td>#<?= $pid ?></td>
                            <td class="admin-table-media">
                                <?php if (!empty($product['image'])): ?>
                                    <img src="<?= product_image($product['image']) ?>" alt="<?= h($product['name']) ?>">
                                <?php else: ?>
                                    <span class="admin-image-placeholder">—</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <strong><?= h($product['name']) ?></strong>
                                <small class="admin-table-sub"><?= h($product['short_description']) ?></small>
                            </td>
                            <td><?= h($product['category_name'] ?? '—') ?></td>
                            <td><strong><?= money($product['price']) ?></strong></td>
                            <td>
                                <span class="admin-stock-pill <?= $stockClass ?>">
                                    <?= $stock ?> шт.
                                </span>
                            </td>
                            <td><?= $product['badge'] !== '' ? '<span class="product-badge inline">' . h($product['badge']) . '</span>' : '—' ?></td>
                            <td class="admin-table-actions">
                                <a class="btn btn-small btn-secondary" href="admin_edit_product.php?id=<?= $pid ?>">Редактировать</a>
                                <a class="btn btn-small btn-ghost-link" href="product.php?id=<?= $pid ?>" target="_blank" rel="noopener">Открыть</a>
                                <form method="POST" action="delete_product.php" class="admin-inline-delete">
                                    <input type="hidden" name="id" value="<?= $pid ?>">
                                    <button class="btn btn-small btn-danger" type="submit">Удалить</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</main>

<?php require 'footer.php'; ?>

</body>
</html>
