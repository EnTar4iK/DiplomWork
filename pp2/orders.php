<?php
session_start();
require 'config/db.php';

if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    header("Location: auth.php");
    exit();
}

$userLogin = $_SESSION['username'];
$userRes = $conn->query("SELECT id FROM users WHERE login = '$userLogin'");
$user = $userRes->fetch_assoc();
$user_id = $user['id'];
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Мои заказы</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>

<?php require 'header.php'; ?>

<h2 style="color:white; text-align:center;">Мои заказы</h2>

<div class="products-container">

<?php
$sql = "SELECT o.*, p.name 
        FROM orders o
        JOIN products p ON o.product_id = p.id
        WHERE o.user_id = $user_id
        ORDER BY o.id DESC";

$result = $conn->query($sql);

while ($row = $result->fetch_assoc()):
?>

<div class="product-card">
    <h3><?= $row['name'] ?></h3>
    <p>Количество: <?= $row['quantity'] ?></p>
    <p>Сумма: <?= $row['total_price'] ?> ₽</p>
    <p>Дата: <?= $row['created_at'] ?></p>
</div>

<?php endwhile; ?>

</div>

</body>
</html>