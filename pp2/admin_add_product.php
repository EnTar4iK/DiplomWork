<?php
require 'auth_admin.php';
require 'config/db.php';
require_once 'functions.php';

$errorMessage = '';
$name = trim($_POST['name'] ?? '');
$price = trim($_POST['price'] ?? '');
$categoryId = (int) ($_POST['category_id'] ?? 0);
$stock = (int) ($_POST['stock'] ?? 1);
$badge = trim($_POST['badge'] ?? '');
$shortDescription = trim($_POST['short_description'] ?? '');
$description = trim($_POST['description'] ?? '');
$categories = fetch_categories($conn);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $imageName = basename((string) ($_FILES['image']['name'] ?? ''));
    $imageTmp = $_FILES['image']['tmp_name'] ?? '';

    if ($name === '' || $price === '' || $shortDescription === '' || $description === '' || $categoryId <= 0 || $imageName === '' || !is_uploaded_file($imageTmp)) {
        $errorMessage = 'Заполните все поля и выберите изображение товара.';
    } else {
        $targetPath = __DIR__ . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . $imageName;

        if (move_uploaded_file($imageTmp, $targetPath)) {
            $priceValue = (int) $price;
            $stmt = $conn->prepare("
                INSERT INTO products (category_id, name, price, short_description, description, image, stock, badge)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->bind_param("isisssis", $categoryId, $name, $priceValue, $shortDescription, $description, $imageName, $stock, $badge);
            $stmt->execute();

            header("Location: admin_products.php");
            exit();
        }

        $errorMessage = 'Не удалось загрузить изображение. Попробуйте ещё раз.';
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Добавить товар</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>

<?php require 'header.php'; ?>

<div class="page-shell admin-page">
    <section class="admin-form-card">
        <div class="hero-kicker">
            <span>Новая карточка</span>
            <span>Фото товара</span>
            <span>Каталог</span>
        </div>
        <p class="admin-eyebrow">Новая позиция</p>
        <h2>Добавить товар</h2>
        <p class="admin-lead">
            Заполните карточку товара и загрузите изображение. После сохранения товар
            появится в каталоге и в административном списке.
        </p>

        <?php if ($errorMessage !== ''): ?>
            <div class="message-box error"><?= htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data" class="admin-form-grid admin-form-single">
            <input
                type="text"
                name="name"
                value="<?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8') ?>"
                placeholder="Название товара"
                required
            >

            <select name="category_id" required>
                <option value="">Категория</option>
                <?php foreach ($categories as $category): ?>
                    <option value="<?= (int) $category['id'] ?>" <?= $categoryId === (int) $category['id'] ? 'selected' : '' ?>>
                        <?= h($category['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <input
                type="number"
                name="price"
                value="<?= htmlspecialchars($price, ENT_QUOTES, 'UTF-8') ?>"
                placeholder="Цена"
                min="0"
                step="1"
                required
            >

            <input
                type="number"
                name="stock"
                value="<?= (int) $stock ?>"
                placeholder="Остаток"
                min="0"
                step="1"
                required
            >

            <input
                type="text"
                name="badge"
                value="<?= h($badge) ?>"
                placeholder="Бейдж (например, Хит)"
            >

            <textarea
                name="short_description"
                rows="3"
                placeholder="Краткое описание"
                required
            ><?= h($shortDescription) ?></textarea>

            <textarea
                name="description"
                rows="6"
                placeholder="Описание товара"
                required
            ><?= htmlspecialchars($description, ENT_QUOTES, 'UTF-8') ?></textarea>

            <input type="file" name="image" accept="image/*" required>

            <div class="button-row">
                <button class="btn" type="submit">Добавить товар</button>
                <a href="admin_products.php" class="btn btn-secondary">К списку товаров</a>
            </div>
        </form>
    </section>
</div>

</body>
</html>
