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

    <main class="page-shell">
        <section class="profile-card">
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
                    $safeLogin = htmlspecialchars((string) $user["login"], ENT_QUOTES, 'UTF-8');
                    $safeRole = htmlspecialchars((string) $_SESSION["role"], ENT_QUOTES, 'UTF-8');
                    $safeTelephone = htmlspecialchars((string) $user["telephone"], ENT_QUOTES, 'UTF-8');

                    echo '
                    <p class="eyebrow">Личный кабинет</p>
                    <h1>Профиль покупателя</h1>
                    <div class="profile-list">
                    <div class="profile-row">
                        <span class="label">Логин</span><br>
                        <span class="value">(' . $safeLogin . ') (' . $safeRole . ')</span>
                    </div>
                    <div class="profile-row"> <span class="label">Телефон</span><br>
                        <span class="value">' . $safeTelephone . '</span>
                    </div>
                    </div>';
                    echo '<a class="btn btn-primary" href="orders.php">Мои заказы</a>';
                } else {
                    echo "Пользователь не найден";
                }
            } 
                else {
                header("Location: auth.php");
                exit();
            }
?>
        </section>
    </main>

</body>
</html>

