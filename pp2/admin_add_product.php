<?php
require 'auth_admin.php';
require 'config/db.php';

$errorMessage = '';
$name = trim($_POST['name'] ?? '');
$price = trim($_POST['price'] ?? '');
$description = trim($_POST['description'] ?? '');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $imageName = basename((string) ($_FILES['image']['name'] ?? ''));
    $imageTmp = $_FILES['image']['tmp_name'] ?? '';

    if ($name === '' || $price === '' || $description === '' || $imageName === '' || !is_uploaded_file($imageTmp)) {
        $errorMessage = 'Заполните все поля и выберите изображение товара.';
    } else {
        $targetPath = __DIR__ . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . $imageName;

        if (move_uploaded_file($imageTmp, $targetPath)) {
            $priceValue = (int) $price;
            $stmt = $conn->prepare("INSERT INTO products (name, price, description, image) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("siss", $name, $priceValue, $description, $imageName);
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

            <input
                type="number"
                name="price"
                value="<?= htmlspecialchars($price, ENT_QUOTES, 'UTF-8') ?>"
                placeholder="Цена"
                min="0"
                step="1"
                required
            >

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
