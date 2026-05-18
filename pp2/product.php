<?php
session_start();
require 'config/db.php';
require_once 'functions.php';

$id = (int) ($_GET['id'] ?? 0);
$stmt = $conn->prepare("
    SELECT p.*, c.name AS category_name
    FROM products p
    LEFT JOIN categories c ON c.id = p.category_id
    WHERE p.id = ?
    LIMIT 1
");
$stmt->bind_param('i', $id);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();
$stmt->close();

if (!$product) {
    http_response_code(404);
}

$specs = [];
if ($product && !empty($product['specs'])) {
    $decodedSpecs = json_decode($product['specs'], true);
    if (is_array($decodedSpecs)) {
        $specs = $decodedSpecs;
    }
}

$related = [];
if ($product) {
    $categoryId = (int) $product['category_id'];
    $productId = (int) $product['id'];
    $relatedResult = $conn->query("
        SELECT p.*, c.name AS category_name
        FROM products p
        LEFT JOIN categories c ON c.id = p.category_id
        WHERE p.category_id = $categoryId AND p.id <> $productId
        ORDER BY p.id DESC
        LIMIT 3
    ");
    $related = $relatedResult ? $relatedResult->fetch_all(MYSQLI_ASSOC) : [];
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $product ? h($product['name']) : 'Товар не найден' ?> — ДАЙКОМ</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
<?php require 'header.php'; ?>

<main class="page-shell">
    <?php if (!$product): ?>
        <section class="empty-state">
            <h1>Товар не найден</h1>
            <p>Позиция могла быть удалена или временно снята с продажи.</p>
            <a class="btn btn-primary" href="products.php">Вернуться в каталог</a>
        </section>
    <?php else: ?>
        <section class="product-detail">
            <div class="product-detail-media">
                <?php if (!empty($product['badge'])): ?>
                    <span class="product-badge"><?= h($product['badge']) ?></span>
                <?php endif; ?>
                <img src="<?= product_image($product['image']) ?>" alt="<?= h($product['name']) ?>">
                <div class="product-media-overlay">
                    <span>Проверка совместимости</span>
                    <span>Настройка перед выдачей</span>
                </div>
            </div>

            <div class="product-detail-info">
                <a class="back-link" href="products.php?category=<?= (int) $product['category_id'] ?>">← <?= h($product['category_name']) ?></a>
                <div class="hero-kicker">
                    <span><?= h($product['category_name']) ?></span>
                    <span>Код <?= (int) $product['id'] ?></span>
                    <span><?= (int) $product['stock'] > 0 ? 'В наличии' : 'Под заказ' ?></span>
                </div>
                <h1><?= h($product['name']) ?></h1>
                <p class="product-lead"><?= h($product['short_description']) ?></p>

                <div class="detail-price-row">
                    <strong><?= money($product['price']) ?></strong>
                    <span><?= (int) $product['stock'] > 0 ? 'В наличии: ' . (int) $product['stock'] . ' шт.' : 'Под заказ' ?></span>
                </div>

                <div class="detail-actions">
                    <a class="btn btn-primary" href="add_to_cart.php?id=<?= (int) $product['id'] ?>">Добавить в корзину</a>
                    <a class="btn btn-glass" href="checkout.php">К оформлению</a>
                </div>

                <div class="delivery-note">
                    <strong>Доставка и оплата</strong>
                    <p>Самовывоз из двух магазинов в Шахтах, курьер по городу, СБП, карта онлайн, оплата при получении или счёт для организации.</p>
                </div>

                <div class="confidence-grid">
                    <div><strong>0 ₽</strong><span>самовывоз</span></div>
                    <div><strong>СЦ</strong><span>сервис рядом</span></div>
                    <div><strong>1 день</strong><span>быстрая выдача</span></div>
                </div>
            </div>
        </section>

        <section class="detail-tabs">
            <article>
                <h2>Описание</h2>
                <p><?= nl2br(h($product['description'])) ?></p>
            </article>

            <article>
                <h2>Характеристики</h2>
                <?php if (empty($specs)): ?>
                    <p>Характеристики уточняются менеджером при подтверждении заказа.</p>
                <?php else: ?>
                    <dl class="spec-list">
                        <?php foreach ($specs as $name => $value): ?>
                            <div>
                                <dt><?= h($name) ?></dt>
                                <dd><?= h($value) ?></dd>
                            </div>
                        <?php endforeach; ?>
                    </dl>
                <?php endif; ?>
            </article>
        </section>

        <?php if (!empty($related)): ?>
            <section class="section-block">
                <div class="section-heading">
                    <p class="eyebrow">Похожие товары</p>
                    <h2>Из этой категории</h2>
                </div>

                <div class="products-grid">
                    <?php foreach ($related as $item): ?>
                        <article class="product-card">
                            <a class="product-media" href="product.php?id=<?= (int) $item['id'] ?>">
                                <img src="<?= product_image($item['image']) ?>" alt="<?= h($item['name']) ?>">
                            </a>
                            <div class="product-body">
                                <span class="product-category"><?= h($item['category_name']) ?></span>
                                <h3><a href="product.php?id=<?= (int) $item['id'] ?>"><?= h($item['name']) ?></a></h3>
                                <p><?= h($item['short_description']) ?></p>
                                <div class="product-footer">
                                    <strong><?= money($item['price']) ?></strong>
                                    <a class="btn btn-small" href="add_to_cart.php?id=<?= (int) $item['id'] ?>">В корзину</a>
                                </div>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endif; ?>
    <?php endif; ?>
</main>

<?php require 'footer.php'; ?>
</body>
</html>
