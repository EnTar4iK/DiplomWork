<?php
session_start();
require 'config/db.php';

if (!empty($_SESSION['logged_in'])) {
    header("Location: index.php");
    exit();
}

$error_message = '';

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['submit_enter'])) {
    $login = trim($_POST['login'] ?? '');
    $password = trim($_POST['password'] ?? '');

    $sql = "SELECT * FROM users WHERE login = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $login);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        if ($password === $user['password']) {
            $_SESSION['logged_in'] = true;
            $_SESSION['username'] = $user['login'];
            $_SESSION['role'] = $user['role'];

            header("Location: index.php");
            exit();
        }

        $error_message = "Неверный пароль";
    } else {
        $error_message = "Пользователь не найден";
    }

    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/styles.css">
    <title>Авторизация</title>
</head>
<body>

<?php require 'header.php'; ?>

<main class="auth-page">
    <section class="auth-card">
        <p class="eyebrow">Личный кабинет</p>
        <h2>Авторизация</h2>
        <p>Войдите, чтобы оформить заказ и посмотреть историю покупок.</p>

        <?php if ($error_message !== ''): ?>
            <div class="error-message"><?= htmlspecialchars($error_message, ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>

        <form method="POST" class="form-stack">
            <div class="form-group">
                <input type="text" name="login" placeholder="Логин" required>
            </div>

            <div class="form-group">
                <input type="password" name="password" placeholder="Пароль" required>
            </div>

            <button class="bton" type="submit" name="submit_enter">Войти</button>
        </form>
    </section>
</main>

</body>
</html>
