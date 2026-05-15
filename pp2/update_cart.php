<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: cart.php');
    exit();
}

if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$quantities = $_POST['quantity'] ?? [];
if (is_array($quantities)) {
    foreach ($quantities as $id => $quantity) {
        $id = (int) $id;
        $quantity = (int) $quantity;

        if ($id <= 0) {
            continue;
        }

        if ($quantity <= 0) {
            unset($_SESSION['cart'][$id]);
        } else {
            $_SESSION['cart'][$id] = min($quantity, 99);
        }
    }
}

header('Location: cart.php');
exit();
