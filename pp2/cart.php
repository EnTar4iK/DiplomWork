<?php
session_start();
require 'config/db.php';

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Корзина</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>

<?php require 'header.php'; ?>

<div class="products-container">

<h2 style="color:white; width:100%; text-align:center;">Корзина</h2>

<?php
$total = 0;

foreach ($_SESSION['cart'] as $id => $qty) {

    $sql = "SELECT * FROM products WHERE id = $id";
    $result = $conn->query($sql);
    $product = $result->fetch_assoc();

    $sum = $product['price'] * $qty;
    $total += $sum;
?>

<div class="product-card">
    <h3><?= $product['name'] ?></h3>
    <p>Цена: <?= $product['price'] ?> ₽</p>
    <p>Количество: <?= $qty ?></p>
    <p>Сумма: <?= $sum ?> ₽</p>
</div>

<?php } ?>

</div>

<h2 style="color:white; text-align:center;">ИТОГО: <?= $total ?> ₽</h2>

<div style="text-align:center; margin-top:20px;">
    <a href="checkout.php" class="btn">Оформить заказ</a>
</div>

</body>
</html>