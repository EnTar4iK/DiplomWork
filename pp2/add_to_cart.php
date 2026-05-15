<?php
session_start();

$id = (int) ($_GET['id'] ?? 0);

if ($id <= 0) {
    header("Location: products.php");
    exit();
}

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if (isset($_SESSION['cart'][$id])) {
    $_SESSION['cart'][$id]++;
} else {
    $_SESSION['cart'][$id] = 1;
}

$redirect = $_SERVER['HTTP_REFERER'] ?? 'products.php';
header("Location: $redirect");
exit();