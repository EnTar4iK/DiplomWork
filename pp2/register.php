<?php
require 'config/db.php';
require_once 'functions.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$successMessage = '';
$errorMessage = '';

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['submit_register'])) {
    $login = trim($_POST['login'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $telephone = trim($_POST['telephone'] ?? '');

    if ($login === '' || $password === '' || $telephone === '') {
        $errorMessage = 'Заполните все поля.';
    } else {
        $stmt = $conn->prepare("INSERT INTO users (login, password, telephone, role) VALUES (?, ?, ?, 'user')");
        $stmt->bind_param("sss", $login, $password, $telephone);

        if ($stmt->execute()) {
            $successMessage = 'Регистрация успешна';
        } else {
            $errorMessage = 'Не удалось зарегистрироваться. Возможно, такой логин уже существует.';
        }

        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/styles.css">
    <title>Регистрация — ДАЙКОМ</title>
</head>
<body>

<?php require 'header.php'; ?>

<main class="auth-page">
    <section class="auth-card">
        <p class="eyebrow">Новый покупатель</p>
        <h1>Регистрация</h1>
        <p>Создайте аккаунт, чтобы быстро оформлять заказы и отслеживать статусы.</p>

        <?php if ($errorMessage !== ''): ?>
            <div class="message-box error"><?= h($errorMessage) ?></div>
        <?php endif; ?>

        <?php if ($successMessage !== ''): ?>
            <div class="message-box success"><?= h($successMessage) ?></div>
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

            <label>
                Телефон
                <input type="tel" name="telephone" placeholder="+7" required>
            </label>

            <button class="btn btn-primary" type="submit" name="submit_register">Зарегистрироваться</button>
            <a class="ghost-link" href="auth.php">Уже есть аккаунт</a>
        </form>

        <?php if ($successMessage !== ''): ?>
            <div class="button-row auth-actions">
                <a href="index.php" class="btn btn-glass">Перейти на сайт</a>
            </div>
        <?php endif; ?>
    </section>
</main>

</body>
</html>
