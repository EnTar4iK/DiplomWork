<?php
require 'auth_admin.php';
require 'config/db.php';

$id = (int)$_POST['id'];
$role = $_POST['role'];

$conn->query("UPDATE users SET role='$role' WHERE id=$id");

header("Location: admin_users.php");
exit();