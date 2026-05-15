<?php
require 'auth_admin.php';
require 'config/db.php';

$id = $_POST['id'];
$name = $_POST['name'];
$price = $_POST['price'];
$description = $_POST['description'];

$stmt = $conn->prepare("UPDATE products SET name=?, price=?, description=? WHERE id=?");
$stmt->bind_param("sdsi", $name, $price, $description, $id);
$stmt->execute();

header("Location: admin_products.php");
exit();