<?php
require 'auth_admin.php';
require 'config/db.php';

$result = $conn->query("SELECT * FROM products ORDER BY id DESC");
$products = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
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
                    Здесь можно быстро редактировать карточки товаров, удалять позиции
                    и переходить к добавлению новых товаров.
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
                                    src="images/<?= rawurlencode($product['image']) ?>"
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
