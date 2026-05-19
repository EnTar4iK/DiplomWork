<?php
require 'auth_admin.php';
require 'config/db.php';
require_once 'functions.php';

$productId = (int) ($_GET['id'] ?? $_POST['id'] ?? 0);

if ($productId <= 0) {
    header('Location: admin_products.php');
    exit();
}

$stmt = $conn->prepare("SELECT * FROM products WHERE id = ? LIMIT 1");
$stmt->bind_param('i', $productId);
$stmt->execute();
$result = $stmt->get_result();
$product = $result ? $result->fetch_assoc() : null;
$stmt->close();

if (!$product) {
    header('Location: admin_products.php');
    exit();
}

$categories = fetch_categories($conn);
$errorMessage = '';
$successMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $categoryId       = (int) ($_POST['category_id'] ?? 0);
    $name             = trim((string) ($_POST['name'] ?? ''));
    $price            = (int) ($_POST['price'] ?? 0);
    $stock            = max(0, (int) ($_POST['stock'] ?? 0));
    $badge            = trim((string) ($_POST['badge'] ?? ''));
    $shortDescription = trim((string) ($_POST['short_description'] ?? ''));
    $description      = trim((string) ($_POST['description'] ?? ''));
    $imageName        = (string) $product['image'];

    if ($categoryId <= 0 || $name === '' || $shortDescription === '' || $description === '') {
        $errorMessage = 'Заполните обязательные поля: категория, название, краткое и полное описание.';
    } else {
        if (!empty($_FILES['image']['name']) && is_uploaded_file($_FILES['image']['tmp_name'] ?? '')) {
            $uploadedName = basename((string) $_FILES['image']['name']);
            $targetPath = __DIR__ . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . $uploadedName;
            if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
                $imageName = $uploadedName;
            } else {
                $errorMessage = 'Не удалось загрузить новое изображение.';
            }
        }

        if ($errorMessage === '') {
            $stmt = $conn->prepare("
                UPDATE products
                SET category_id = ?, name = ?, price = ?, stock = ?, badge = ?, short_description = ?, description = ?, image = ?
                WHERE id = ?
            ");
            $stmt->bind_param(
                'isiissssi',
                $categoryId, $name, $price, $stock, $badge, $shortDescription, $description, $imageName, $productId
            );
            $stmt->execute();
            $stmt->close();

            header('Location: admin_products.php?updated=' . $productId);
            exit();
        }
    }

    $product['category_id']       = $categoryId;
    $product['name']              = $name;
    $product['price']             = $price;
    $product['stock']             = $stock;
    $product['badge']             = $badge;
    $product['short_description'] = $shortDescription;
    $product['description']       = $description;
    $product['image']             = $imageName;
}

$adminActive = 'products';
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Редактирование товара — ДАЙКОМ</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>

<?php require 'header.php'; ?>

<main class="page-shell admin-page">
    <header class="admin-page-head">
        <div>
            <p class="admin-eyebrow">Каталог</p>
            <h1>Товар #<?= (int) $product['id'] ?></h1>
            <p class="admin-lead">Обновите данные карточки и при необходимости загрузите новое изображение.</p>
        </div>
        <div class="admin-page-head-actions">
            <a class="btn btn-secondary" href="admin_products.php">К списку товаров</a>
            <a class="btn btn-ghost-link" href="product.php?id=<?= (int) $product['id'] ?>" target="_blank" rel="noopener">Открыть на сайте</a>
        </div>
    </header>

    <?php require 'admin_nav.php'; ?>

    <?php if ($errorMessage !== ''): ?>
        <div class="message-box error"><?= h($errorMessage) ?></div>
    <?php endif; ?>

    <section class="admin-edit-card">
        <form method="POST" enctype="multipart/form-data" class="admin-edit-form">
            <input type="hidden" name="id" value="<?= (int) $product['id'] ?>">

            <div class="admin-edit-grid">
                <div class="admin-edit-image">
                    <?php if (!empty($product['image'])): ?>
                        <img src="<?= product_image($product['image']) ?>" alt="<?= h($product['name']) ?>">
                    <?php else: ?>
                        <div class="admin-image-placeholder">Без изображения</div>
                    <?php endif; ?>

                    <label class="admin-field">
                        <span>Новое изображение (необязательно)</span>
                        <input type="file" name="image" accept="image/*">
                    </label>
                </div>

                <div class="admin-edit-fields">
                    <label class="admin-field">
                        <span>Название</span>
                        <input type="text" name="name" value="<?= h($product['name']) ?>" required>
                    </label>

                    <label class="admin-field">
                        <span>Категория</span>
                        <select name="category_id" required>
                            <option value="">Выберите категорию</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?= (int) $category['id'] ?>" <?= (int) $product['category_id'] === (int) $category['id'] ? 'selected' : '' ?>>
                                    <?= h($category['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </label>

                    <div class="admin-edit-row">
                        <label class="admin-field">
                            <span>Цена, ₽</span>
                            <input type="number" name="price" min="0" step="1" value="<?= (int) $product['price'] ?>" required>
                        </label>

                        <label class="admin-field">
                            <span>Остаток, шт.</span>
                            <input type="number" name="stock" min="0" step="1" value="<?= (int) $product['stock'] ?>" required>
                        </label>

                        <label class="admin-field">
                            <span>Бейдж</span>
                            <input type="text" name="badge" value="<?= h($product['badge']) ?>" placeholder="Хит, Топ, Новинка…">
                        </label>
                    </div>

                    <label class="admin-field">
                        <span>Краткое описание</span>
                        <textarea name="short_description" rows="3" required><?= h($product['short_description']) ?></textarea>
                    </label>

                    <label class="admin-field">
                        <span>Полное описание</span>
                        <textarea name="description" rows="8" required><?= h($product['description']) ?></textarea>
                    </label>
                </div>
            </div>

            <div class="admin-edit-actions">
                <button class="btn btn-primary" type="submit">Сохранить изменения</button>
                <a class="btn btn-secondary" href="admin_products.php">Отменить</a>
            </div>
        </form>

        <form method="POST" action="delete_product.php" class="admin-edit-delete">
            <input type="hidden" name="id" value="<?= (int) $product['id'] ?>">
            <button class="btn btn-danger" type="submit">Удалить товар</button>
        </form>
    </section>
</main>

<?php require 'footer.php'; ?>

</body>
</html>
