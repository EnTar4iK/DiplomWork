<?php
session_start();
require 'config/db.php';

if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    header("Location: auth.php");
    exit();
}

$userLogin = $_SESSION['username'];

// получаем user_id
$userRes = $conn->query("SELECT id FROM users WHERE login = '$userLogin'");
$user = $userRes->fetch_assoc();
$user_id = $user['id'];

if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    header("Location: cart.php");
    exit();
}

foreach ($_SESSION['cart'] as $id => $qty) {

    $productRes = $conn->query("SELECT price FROM products WHERE id = $id");
    $product = $productRes->fetch_assoc();

    $total = $product['price'] * $qty;

    $conn->query("
        INSERT INTO orders (user_id, product_id, quantity, total_price)
        VALUES ($user_id, $id, $qty, $total)
    ");
}

// очистка корзины
unset($_SESSION['cart']);

header("Location: orders.php");
exit();
?>