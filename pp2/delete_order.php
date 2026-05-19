<?php
require 'auth_admin.php';
require 'config/db.php';

$id = (int) ($_POST['id'] ?? $_GET['id'] ?? 0);

if ($id > 0) {
    $stmt = $conn->prepare("DELETE FROM order_items WHERE order_id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->close();

    $stmt = $conn->prepare("DELETE FROM orders WHERE id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->close();
}

$returnQuery = (string) ($_POST['return_query'] ?? $_SERVER['QUERY_STRING'] ?? '');
parse_str($returnQuery, $params);
unset($params['id']);
$qs = http_build_query($params);

header('Location: admin_orders.php' . ($qs !== '' ? '?' . $qs : ''));
exit();
