<?php
require 'auth_admin.php';
require 'config/db.php';
require_once 'functions.php';

$result = $conn->query("
    SELECT p.*, c.name AS category_name
    FROM products p
    LEFT JOIN categories c ON c.id = p.category_id
    ORDER BY p.id DESC
");
$products = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
$categories = fetch_categories($conn);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Управление товарами</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>

<?php require 'header.php'; ?>

<div class="page-shell admin-page">
    <section class="admin-section">
        <div class="admin-toolbar">
            <div>
                <p class="admin-eyebrow">Каталог</p>
                <h2>Управление товарами</h2>
                <p class="admin-lead">
                    Здесь можно быстро редактировать карточки товаров, наличие, категории и изображения.
                </p>
            </div>

            <a href="admin_add_product.php" class="admin-btn">Добавить товар</a>
        </div>

        <?php if (empty($products)): ?>
            <div class="admin-empty">
                <h3>Товаров пока нет</h3>
                <p>Добавьте первую позицию, и она сразу появится в этом разделе.</p>
            </div>
        <?php else: ?>
            <div class="admin-card-grid">
                <?php foreach ($products as $product): ?>
                    <article class="admin-product-card">
                        <div class="admin-product-media">
                            <?php if (!empty($product['image'])): ?>
                                <img
                                    class="product-img"
                                    src="<?= product_image($product['image']) ?>"
                                    alt="<?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8') ?>"
                                >
                            <?php else: ?>
                                <div class="admin-image-placeholder">Без изображения</div>
                            <?php endif; ?>
                        </div>

                        <div class="admin-product-body">
                            <span class="admin-card-label">Товар #<?= (int) $product['id'] ?></span>

                            <form method="POST" action="update_product.php" class="admin-form-grid">
                                <input type="hidden" name="id" value="<?= (int) $product['id'] ?>">

                                <select name="category_id" required>
                                    <?php foreach ($categories as $category): ?>
                                        <option value="<?= (int) $category['id'] ?>" <?= (int) $product['category_id'] === (int) $category['id'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($category['name'], ENT_QUOTES, 'UTF-8') ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>

                                <input
                                    type="text"
                                    name="name"
                                    value="<?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8') ?>"
                                    placeholder="Название"
                                    required
                                >

                                <input
                                    type="number"
                                    name="price"
                                    value="<?= (int) $product['price'] ?>"
                                    placeholder="Цена"
                                    min="0"
                                    step="1"
                                    required
                                >

                                <input
                                    type="number"
                                    name="stock"
                                    value="<?= (int) $product['stock'] ?>"
                                    placeholder="Остаток"
                                    min="0"
                                    step="1"
                                    required
                                >

                                <input
                                    type="text"
                                    name="badge"
                                    value="<?= htmlspecialchars($product['badge'], ENT_QUOTES, 'UTF-8') ?>"
                                    placeholder="Бейдж"
                                >

                                <textarea
                                    name="short_description"
                                    rows="3"
                                    placeholder="Краткое описание"
                                    required
                                ><?= htmlspecialchars($product['short_description'], ENT_QUOTES, 'UTF-8') ?></textarea>

                                <textarea
                                    name="description"
                                    rows="5"
                                    placeholder="Описание"
                                    required
                                ><?= htmlspecialchars($product['description'], ENT_QUOTES, 'UTF-8') ?></textarea>

                                <div class="button-row">
                                    <button class="btn" type="submit">Редактировать</button>
                                    <a
                                        href="delete_product.php?id=<?= (int) $product['id'] ?>"
                                        class="btn btn-danger"
                                        onclick="return confirm('Удалить товар?')"
                                    >
                                        Удалить
                                    </a>
                                </div>
                            </form>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>
</div>

</body>
</html>
