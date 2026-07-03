<?php

declare(strict_types=1);

use App\Controllers\CartController;
use App\Controllers\OrderController;

require_once dirname(__DIR__, 2) . '/vendor/autoload.php';
require_once dirname(__DIR__, 2) . '/app/Config/config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$userId = $_SESSION['user_id'] ?? 0;

if ($userId === 0) {
    header('Location: /login.php');
    exit;
}

$cartController = new CartController();
$orderController = new OrderController();
$items = $cartController->list($userId);
$errors = [];
$values = [
    'shipping_address' => '',
    'phone' => '',
    'payment_method' => '',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $values = array_map(static fn ($value) => trim((string) $value), [
        'shipping_address' => $_POST['shipping_address'] ?? '',
        'phone' => $_POST['phone'] ?? '',
        'payment_method' => $_POST['payment_method'] ?? '',
    ]);

    foreach ($values as $field => $value) {
        if ($value === '') {
            $label = str_replace('_', ' ', ucfirst($field));
            $errors[$field] = $label . ' is required.';
        }
    }

    if (empty($errors) && empty($items)) {
        $errors['cart'] = 'Your cart must contain at least one item to place an order.';
    }

    if (empty($errors)) {
        try {
            $orderId = $orderController->checkout($userId, $values);
            header('Location: /order.php?id=' . (int) $orderId);
            exit;
        } catch (Throwable $exception) {
            $errors['general'] = 'Unable to complete your order. Please try again.';
        }
    }
}

$total = array_reduce($items, static fn ($carry, $item) => $carry + ((float) $item['price'] * (int) $item['quantity']), 0.0);
?>

<!DOCTYPE html>
<html lang="en">
<?php
$pageTitle = 'Checkout | Online Book Store';
$pageStyles = ['/assets/css/checkout.css'];
?>
<?php include dirname(__DIR__, 2) . '/views/layouts/head.php'; ?>
<body>
    <?php include dirname(__DIR__, 2) . '/views/layouts/flash.php'; ?>
    <div class="page-shell">
        <?php include dirname(__DIR__, 2) . '/views/layouts/header.php'; ?>

        <section class="checkout-grid">
            <div class="checkout-form-shell">
                <h1>Checkout details</h1>
                <p class="checkout-copy">Complete your shipping information and place your order.</p>

                <?php if (!empty($errors['general'])): ?>
                    <div class="form-error"><?= htmlspecialchars($errors['general'], ENT_QUOTES, 'UTF-8'); ?></div>
                <?php endif; ?>

                <?php if (!empty($errors['cart'])): ?>
                    <div class="form-error"><?= htmlspecialchars($errors['cart'], ENT_QUOTES, 'UTF-8'); ?></div>
                <?php endif; ?>

                <form class="checkout-form" method="post" action="" novalidate>
                    <?php foreach (['shipping_address' => 'Shipping Address', 'phone' => 'Phone', 'payment_method' => 'Payment Method'] as $name => $label): ?>
                        <div class="input-group">
                            <label class="input-label" for="<?= $name; ?>"><?= $label; ?></label>
                            <?php if ($name === 'payment_method'): ?>
                                <select class="input-field" id="<?= $name; ?>" name="<?= $name; ?>">
                                    <option value="" <?= $values[$name] === '' ? 'selected' : ''; ?>>Select payment method</option>
                                    <option value="Credit Card" <?= $values[$name] === 'Credit Card' ? 'selected' : ''; ?>>Credit Card</option>
                                    <option value="PayPal" <?= $values[$name] === 'PayPal' ? 'selected' : ''; ?>>PayPal</option>
                                    <option value="Cash on Delivery" <?= $values[$name] === 'Cash on Delivery' ? 'selected' : ''; ?>>Cash on Delivery</option>
                                </select>
                            <?php else: ?>
                                <input class="input-field" type="text" id="<?= $name; ?>" name="<?= $name; ?>" value="<?= htmlspecialchars($values[$name], ENT_QUOTES, 'UTF-8'); ?>">
                            <?php endif; ?>
                            <?php if (!empty($errors[$name])): ?>
                                <div class="field-error"><?= htmlspecialchars($errors[$name], ENT_QUOTES, 'UTF-8'); ?></div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>

                    <button class="checkout-button" type="submit">Place Order</button>
                </form>
            </div>

            <aside class="order-summary-shell">
                <div class="order-summary-card">
                    <h2>Order summary</h2>
                    <?php if (empty($items)): ?>
                        <p class="checkout-copy">Add items to your cart before placing an order.</p>
                    <?php else: ?>
                        <ul class="summary-list">
                            <?php foreach ($items as $item): ?>
                                <li class="summary-item">
                                    <img class="summary-image" src="<?= htmlspecialchars($item['cover_image'], ENT_QUOTES, 'UTF-8'); ?>" alt="<?= htmlspecialchars($item['title'], ENT_QUOTES, 'UTF-8'); ?>">
                                    <div>
                                        <p class="summary-title"><?= htmlspecialchars($item['title'], ENT_QUOTES, 'UTF-8'); ?></p>
                                        <p class="summary-quantity">Qty: <?= (int) $item['quantity']; ?></p>
                                    </div>
                                    <p class="summary-price">$<?= number_format((float) $item['price'] * (int) $item['quantity'], 2); ?></p>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>

                    <div class="summary-footer">
                        <p>Total</p>
                        <p>$<?= number_format($total, 2); ?></p>
                    </div>
                </div>
            </aside>
        </section>

        <?php include dirname(__DIR__, 2) . '/views/layouts/footer.php'; ?>
    </div>
    <?php include dirname(__DIR__, 2) . '/views/layouts/scripts.php'; ?>
</body>
</html>
