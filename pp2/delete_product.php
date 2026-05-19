<?php
require 'auth_admin.php';
require 'config/db.php';

$id = (int) ($_POST['id'] ?? $_GET['id'] ?? 0);

if ($id > 0) {
    $stmt = $conn->prepare("SELECT image FROM products WHERE id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        if ($row['image'] && strpos($row['image'], 'http') !== 0 && file_exists("images/" . $row['image'])) {
            unlink("images/" . $row['image']);
        }
    }
    $stmt->close();

    $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->close();
}

header("Location: admin_products.php");
exit();
