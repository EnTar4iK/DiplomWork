<?php
require 'auth_admin.php';
require 'config/db.php';

$id = (int)$_GET['id'];

// удалить картинку
$result = $conn->query("SELECT image FROM products WHERE id=$id");
if ($row = $result->fetch_assoc()) {
    if ($row['image'] && strpos($row['image'], 'http') !== 0 && file_exists("images/" . $row['image'])) {
        unlink("images/" . $row['image']);
    }
}

$stmt = $conn->prepare("DELETE FROM products WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();

header("Location: admin_products.php");
exit();