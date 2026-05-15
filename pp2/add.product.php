<?php
require 'auth_admin.php';
require 'config/db.php';

$name = $_POST['name'];
$price = $_POST['price'];
$description = $_POST['description'];

$image = $_FILES['image']['name'];
$tmp = $_FILES['image']['tmp_name'];

move_uploaded_file($tmp, "images/" . $image);

$stmt = $conn->prepare("INSERT INTO products (name, price, description, image) VALUES (?, ?, ?, ?)");
$stmt->bind_param("sdss", $name, $price, $description, $image);
$stmt->execute();

header("Location: admin_products.php");
exit();