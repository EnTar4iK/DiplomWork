<?php
session_start();
require 'config/db.php';
require_once 'functions.php';

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

            $redirect = ($user['role'] === 'admin') ? 'profile.php' : 'index.php';
            header("Location: $redirect");
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
    <title>Вход — ДАЙКОМ</title>
</head>
<body>

<?php require 'header.php'; ?>

<main class="auth-page">
    <section class="auth-card">
        <p class="eyebrow">Личный кабинет</p>
        <h1>Вход в ДАЙКОМ</h1>
        <p>Войдите, чтобы оформить заказ, отслеживать доставку и видеть историю покупок.</p>

        <?php if ($error_message !== ''): ?>
            <div class="message-box error"><?= h($error_message) ?></div>
        <?php endif; ?>

        <form method="POST" class="auth-form">
            <label>
                Логин
                <input type="text" name="login" placeholder="Логин" required>
            </label>

            <label>
                Пароль
                <input type="password" name="password" placeholder="Пароль" required>
            </label>

            <button class="btn btn-primary" type="submit" name="submit_enter">Войти</button>
            <a class="ghost-link" href="register.php">Создать аккаунт</a>
        </form>
    </section>
</main>

</body>
</html>
