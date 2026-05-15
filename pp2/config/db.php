<?php

$host = "localhost";
$login = "root";
$password = "";
$db_name = "PP2";

$conn = new mysqli($host, $login, $password, $db_name);

	if ($conn->connect_error)  {
		die("Ошибка подключения" . $conn->connect_error);
	}
?>