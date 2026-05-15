<?php 
    session_start();
    require 'config/db.php'; 
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/styles.css">
    <title>Личный кабинет</title>
</head>
<body>
    <?php require 'header.php';?>

    <div class="profile">
        <?php
            require 'config/db.php';

            if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']) {

                    $login = $_SESSION['username'];

                    $sql = "SELECT * FROM users WHERE login = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("s", $login);
                    $stmt->execute();
                    $result = $stmt->get_result();

                if ($result->num_rows == 1) {
                    $user = $result->fetch_assoc();
                
                    echo '
                    <li>
                        <span class="label">Логин</span><br>
                        <span class="value">(' . $user["login"] . ') (' . $_SESSION["role"] . ')</span>
                    </li>
                    <li> <span class="label">Телефон</span><br>
                        <span class="value">' . $user["telephone"] . '</span>
                    </li>';
                    echo '<a class="btno" href="orders.php">Мои заказы</a>';
                } else {
                    echo "Пользователь не найден";
                }
            } 
                else {
                header("Location: auth.php");
                exit();
            }
?>
    </div>

</body>
</html>

