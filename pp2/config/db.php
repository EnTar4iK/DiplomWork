<?php

$host = "localhost";
$login = "root";
$password = "";
$db_name = "PP2";

$conn = new mysqli($host, $login, $password, $db_name);
$conn->set_charset("utf8mb4");

	if ($conn->connect_error)  {
		die("Ошибка подключения" . $conn->connect_error);
	}
?>