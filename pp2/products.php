<?php
session_start();
require 'config/db.php';
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Товары</title>
    <link rel="stylesheet" href="css/styles.css">
</head>

<body>

<?php require 'header.php'; ?>

<?php
$sort = $_GET['sort'] ?? '';
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
?>

<form method="GET" class="sort-form">
    <select name="sort" onchange="this.form.submit()">
        <option value="">Сортировка</option>
        <option value="price_asc">Цена ↑</option>
        <option value="price_desc">Цена ↓</option>
        <option value="name_asc">Имя A-Z</option>
    </select>
</form>

<div class="products-container">

<?php
$sql = "SELECT * FROM products $orderBy";
$result = $conn->query($sql);

while ($row = $result->fetch_assoc()):
?>

<div class="product-card">
    <img class="product-img" src="images/<?= $row['image'] ?>" alt="">

    <h3><?= $row['name'] ?></h3>

    <div class="price"><?= $row['price'] ?> ₽</div>

    <button class="btn" onclick="addToCart(<?= $row['id'] ?>)">Добавить в корзину</button>

    <button class="btn" onclick="openModal('<?= htmlspecialchars($row['description']) ?>')">
        Подробнее
    </button>
</div>

<?php endwhile; ?>

</div>

<!-- MODAL -->
<div id="modal" class="modal" onclick="closeModal()">
    <div class="modal-content" onclick="event.stopPropagation()">
        <p id="modal-text"></p>
    </div>
</div>

<script>
function addToCart(id) {
    window.location.href = "add_to_cart.php?id=" + id;
}

function openModal(text) {
    document.getElementById("modal").style.display = "flex";
    document.getElementById("modal-text").innerText = text;
}

function closeModal() {
    document.getElementById("modal").style.display = "none";
}
</script>

</body>
</html>