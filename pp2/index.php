<?php session_start(); ?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/styles.css">
    <title>Магазин электроники</title>
</head>
<body>
    <?php require 'header.php'; ?>
    <main>
        <div class="slide-item">
            <div class="hero-content">
                <h1>Добро пожаловать в наш магазин электроники</h1>
                <p>Лучшие гаджеты и техника ждут вас именно здесь</p>
                <a class="btn" href="<?php echo (isset($_SESSION['logged_in']) && $_SESSION['logged_in']) ? 'products.php' : 'auth.php'; ?>">Приступим</a>
            </div>
        </div>
    </main>

</body>
</html>