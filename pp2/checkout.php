<?php
session_start();
require 'config/db.php';
require_once 'functions.php';

require_login();

if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    header("Location: cart.php");
    exit();
}

$user = current_user($conn);
$cart = get_cart_items($conn);
$deliveryMethods = delivery_methods();
$paymentMethods = payment_methods();
$errors = [];
$successOrderId = 0;

if (!$user) {
    header("Location: auth.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customerName = trim($_POST['customer_name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $deliveryMethod = (string) ($_POST['delivery_method'] ?? '');
    $paymentMethod = (string) ($_POST['payment_method'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $comment = trim($_POST['comment'] ?? '');

    if ($customerName === '') {
        $errors[] = 'Укажите имя получателя.';
    }

    if ($phone === '') {
        $errors[] = 'Укажите телефон для подтверждения заказа.';
    }

    if (!isset($deliveryMethods[$deliveryMethod])) {
        $errors[] = 'Выберите способ доставки.';
    }

    if (!isset($paymentMethods[$paymentMethod])) {
        $errors[] = 'Выберите способ оплаты.';
    }

    if (empty($cart['items'])) {
        $errors[] = 'Корзина пуста.';
    }

    if (empty($errors)) {
        $status = in_array($paymentMethod, ['card_online', 'sbp'], true) ? 'paid' : 'new';
        $stmt = $conn->prepare("
            INSERT INTO orders
                (user_id, customer_name, phone, email, delivery_method, payment_method, delivery_address, comment, total_price, status)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $total = (int) $cart['total'];
        $stmt->bind_param(
            'isssssssis',
            $user['id'],
            $customerName,
            $phone,
            $email,
            $deliveryMethod,
            $paymentMethod,
            $address,
            $comment,
            $total,
            $status
        );
        $stmt->execute();
        $successOrderId = $stmt->insert_id;
        $stmt->close();

        $itemStmt = $conn->prepare("
            INSERT INTO order_items (order_id, product_id, product_name, price, quantity, total_price)
            VALUES (?, ?, ?, ?, ?, ?)
        ");

        foreach ($cart['items'] as $item) {
            $productId = (int) $item['id'];
            $productName = (string) $item['name'];
            $price = (int) $item['price'];
            $quantity = (int) $item['quantity'];
            $lineTotal = (int) $item['line_total'];
            $itemStmt->bind_param('iisiii', $successOrderId, $productId, $productName, $price, $quantity, $lineTotal);
            $itemStmt->execute();
        }

        $itemStmt->close();
        unset($_SESSION['cart']);
        header("Location: orders.php?created=$successOrderId");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Оформление заказа — DАЙКОМ Store</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
<?php require 'header.php'; ?>

<main class="page-shell checkout-page">
    <section class="page-hero compact">
        <p class="eyebrow">Checkout</p>
        <h1>Оформление заказа</h1>
        <p>Выберите удобный способ оплаты и доставки. После отправки менеджер DАЙКОМ подтвердит наличие и детали.</p>
    </section>

    <?php if (!empty($errors)): ?>
        <div class="message-box error">
            <?php foreach ($errors as $error): ?>
                <p><?= h($error) ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form method="POST" class="checkout-layout">
        <section class="checkout-form">
            <div class="form-panel">
                <h2>Контакты</h2>
                <label>
                    Имя получателя
                    <input type="text" name="customer_name" value="<?= h($_POST['customer_name'] ?? $user['login']) ?>" required>
                </label>
                <label>
                    Телефон
                    <input type="tel" name="phone" value="<?= h($_POST['phone'] ?? $user['telephone']) ?>" required>
                </label>
                <label>
                    Email
                    <input type="email" name="email" value="<?= h($_POST['email'] ?? '') ?>" placeholder="для электронного чека">
                </label>
            </div>

            <div class="form-panel">
                <h2>Доставка</h2>
                <div class="option-grid">
                    <?php foreach ($deliveryMethods as $key => $label): ?>
                        <label class="option-card">
                            <input type="radio" name="delivery_method" value="<?= h($key) ?>" <?= ($_POST['delivery_method'] ?? 'pickup_daycom') === $key ? 'checked' : '' ?>>
                            <span><?= h($label) ?></span>
                        </label>
                    <?php endforeach; ?>
                </div>
                <label>
                    Адрес доставки
                    <textarea name="address" rows="3" placeholder="Заполните для курьера или транспортной компании"><?= h($_POST['address'] ?? '') ?></textarea>
                </label>
            </div>

            <div class="form-panel">
                <h2>Оплата</h2>
                <div class="option-grid">
                    <?php foreach ($paymentMethods as $key => $label): ?>
                        <label class="option-card">
                            <input type="radio" name="payment_method" value="<?= h($key) ?>" <?= ($_POST['payment_method'] ?? 'card_online') === $key ? 'checked' : '' ?>>
                            <span><?= h($label) ?></span>
                        </label>
                    <?php endforeach; ?>
                </div>
                <label>
                    Комментарий
                    <textarea name="comment" rows="3" placeholder="Например: нужна установка Windows или звонок после 14:00"><?= h($_POST['comment'] ?? '') ?></textarea>
                </label>
            </div>
        </section>

        <aside class="cart-summary checkout-summary">
            <h2>Ваш заказ</h2>
            <?php foreach ($cart['items'] as $item): ?>
                <div class="checkout-item">
                    <span><?= h($item['name']) ?> × <?= (int) $item['quantity'] ?></span>
                    <strong><?= money($item['line_total']) ?></strong>
                </div>
            <?php endforeach; ?>
            <div class="summary-row total">
                <span>Итого</span>
                <strong><?= money($cart['total']) ?></strong>
            </div>
            <p>Онлайн-оплата имитируется для учебного проекта: заказ сразу получает статус «Оплачен онлайн».</p>
            <button class="btn btn-primary" type="submit">Подтвердить заказ</button>
        </aside>
    </form>
</main>

<?php require 'footer.php'; ?>
</body>
</html>