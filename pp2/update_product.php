<?php
require 'auth_admin.php';
require 'config/db.php';

$id = (int) ($_POST['id'] ?? 0);
$categoryId = (int) ($_POST['category_id'] ?? 0);
$name = trim($_POST['name'] ?? '');
$price = (int) ($_POST['price'] ?? 0);
$stock = (int) ($_POST['stock'] ?? 0);
$badge = trim($_POST['badge'] ?? '');
$shortDescription = trim($_POST['short_description'] ?? '');
$description = trim($_POST['description'] ?? '');

if ($id > 0 && $categoryId > 0 && $name !== '' && $shortDescription !== '' && $description !== '') {
    $stmt = $conn->prepare("
        UPDATE products
        SET category_id = ?, name = ?, price = ?, stock = ?, badge = ?, short_description = ?, description = ?
        WHERE id = ?
    ");
    $stmt->bind_param("isiisssi", $categoryId, $name, $price, $stock, $badge, $shortDescription, $description, $id);
    $stmt->execute();
}

header("Location: admin_products.php");
exit();