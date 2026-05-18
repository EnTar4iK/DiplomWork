<?php
require 'auth_admin.php';
require 'config/db.php';

$id = (int) ($_GET['id'] ?? 0);

if ($id > 0) {
    // order_items have ON DELETE CASCADE, but be explicit anyway for clarity.
    $stmt = $conn->prepare("DELETE FROM order_items WHERE order_id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->close();

    $stmt = $conn->prepare("DELETE FROM orders WHERE id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->close();
}

$queryString = $_SERVER['QUERY_STRING'] ?? '';
// Strip the id param when redirecting back so we keep the user's filters.
parse_str($queryString, $params);
unset($params['id']);
$qs = http_build_query($params);

header('Location: admin_orders.php' . ($qs !== '' ? '?' . $qs : ''));
exit();
