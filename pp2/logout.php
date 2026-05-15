<?php
session_start();

unset($_SESSION['logged_in']);
unset($_SESSION['username']);
unset($_SESSION['role']);

session_destroy();

header("Location: index.php");
exit();
?>