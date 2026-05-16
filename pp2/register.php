<?php
require 'config/db.php';

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
    <title>Регистрация</title>
</head>
<body>

<?php require 'header.php'; ?>

<main class="auth-page">
    <section class="auth-card register-card">
        <p class="eyebrow">Новый аккаунт</p>
        <h2>Регистрация</h2>
        <p>Создайте профиль, чтобы оформлять заказы и возвращаться к истории покупок.</p>

        <?php if ($errorMessage !== ''): ?>
            <div class="error-message"><?= htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>

        <?php if ($successMessage !== ''): ?>
            <div class="success-message"><?= htmlspecialchars($successMessage, ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>

        <form method="POST" class="form-stack">
            <div class="form-reg">
                <input type="text" name="login" placeholder="Логин" required>
            </div>

            <div class="form-reg">
                <input type="password" name="password" placeholder="Пароль" required>
            </div>

            <div>
                <input type="tel" name="telephone" placeholder="+7" required>
            </div>

            <button class="bton" type="submit" name="submit_register">Зарегистрироваться</button>
        </form>

        <?php if ($successMessage !== ''): ?>
            <div class="button-row auth-actions">
                <a href="index.php" class="btn">Перейти на сайт</a>
            </div>
        <?php endif; ?>
    </section>
</main>

</body>
</html>
