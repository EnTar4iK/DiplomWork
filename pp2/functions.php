<?php

function h($value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function money($amount): string
{
    return number_format((float) $amount, 0, '', ' ') . ' ₽';
}

function product_image(string $image): string
{
    if (strpos($image, 'http://') === 0 || strpos($image, 'https://') === 0) {
        return h($image);
    }

    return 'images/' . rawurlencode($image);
}

function cart_count(): int
{
    if (empty($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
        return 0;
    }

    $count = 0;
    foreach ($_SESSION['cart'] as $quantity) {
        $count += (int) $quantity;
    }

    return $count;
}

function fetch_categories(mysqli $conn): array
{
    $result = $conn->query('SELECT * FROM categories ORDER BY name');

    return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
}

function fetch_featured_products(mysqli $conn, int $limit = 6): array
{
    $limit = max(1, $limit);
    $result = $conn->query("
        SELECT p.*, c.name AS category_name
        FROM products p
        LEFT JOIN categories c ON c.id = p.category_id
        ORDER BY p.badge <> '' DESC, p.id DESC
        LIMIT $limit
    ");

    return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
}

function get_cart_items(mysqli $conn): array
{
    if (empty($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
        return ['items' => [], 'total' => 0];
    }

    $ids = [];
    foreach (array_keys($_SESSION['cart']) as $id) {
        $id = (int) $id;
        if ($id > 0) {
            $ids[] = $id;
        }
    }

    if (empty($ids)) {
        $_SESSION['cart'] = [];
        return ['items' => [], 'total' => 0];
    }

    $sqlIds = implode(',', $ids);
    $result = $conn->query("
        SELECT p.*, c.name AS category_name
        FROM products p
        LEFT JOIN categories c ON c.id = p.category_id
        WHERE p.id IN ($sqlIds)
    ");

    $items = [];
    $total = 0;

    if ($result) {
        while ($product = $result->fetch_assoc()) {
            $quantity = max(1, (int) ($_SESSION['cart'][(int) $product['id']] ?? 1));
            $lineTotal = (int) $product['price'] * $quantity;
            $product['quantity'] = $quantity;
            $product['line_total'] = $lineTotal;
            $items[] = $product;
            $total += $lineTotal;
        }
    }

    return ['items' => $items, 'total' => $total];
}

function current_user(mysqli $conn): ?array
{
    if (empty($_SESSION['logged_in']) || empty($_SESSION['username'])) {
        return null;
    }

    $login = (string) $_SESSION['username'];
    $stmt = $conn->prepare('SELECT * FROM users WHERE login = ? LIMIT 1');
    $stmt->bind_param('s', $login);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();

    return $user ?: null;
}

function require_login(): void
{
    if (empty($_SESSION['logged_in'])) {
        header('Location: auth.php');
        exit();
    }
}

function order_statuses(): array
{
    return [
        'new' => 'Новый',
        'paid' => 'Оплачен онлайн',
        'processing' => 'В обработке',
        'delivery' => 'Передан в доставку',
        'done' => 'Выполнен',
        'cancelled' => 'Отменён',
    ];
}

function payment_methods(): array
{
    return [
        'card_online' => 'Банковская карта онлайн',
        'sbp' => 'СБП',
        'cash_store' => 'Оплата в магазине',
        'card_courier' => 'Карта курьеру',
        'invoice' => 'Счёт для организации',
    ];
}

function delivery_methods(): array
{
    return [
        'pickup_daycom' => 'Самовывоз: ДАЙКОМ, ул. Советская, 214-а',
        'pickup_world' => 'Самовывоз: Компьютерный мир, пр. Победа Революции, 85',
        'courier_shakhty' => 'Курьер по г. Шахты',
        'transport' => 'Транспортная компания по России',
    ];
}

function checkout_steps(): array
{
    return [
        ['01', 'Каталог', 'Выберите товар, сравните характеристики и добавьте в корзину.'],
        ['02', 'Оформление', 'Укажите контакты, способ доставки и удобную оплату.'],
        ['03', 'Подтверждение', 'Менеджер проверит наличие и согласует детали заказа.'],
        ['04', 'Получение', 'Заберите покупку в магазине или дождитесь доставки.'],
    ];
}

function trust_features(): array
{
    return [
        ['27+', 'лет опыта', 'ДАЙКОМ работает в Шахтах с 1997 года и обслуживает частных и корпоративных клиентов.'],
        ['2', 'точки выдачи', 'Самовывоз из ДАЙКОМ на Советской и магазина «Компьютерный мир».'],
        ['24/7', 'онлайн-заказ', 'Сайт принимает заявки в любое время, менеджер подтверждает их в рабочие часы.'],
        ['СЦ', 'сервис рядом', 'Диагностика, ремонт, настройка, перенос данных и подготовка техники к работе.'],
    ];
}
