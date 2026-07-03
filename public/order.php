<?php

declare(strict_types=1);

use App\Controllers\OrderController;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../app/Config/config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$userId = $_SESSION['user_id'] ?? 0;
$orderId = isset($_GET['id']) && is_numeric($_GET['id']) ? (int) $_GET['id'] : 0;

if ($userId === 0 || $orderId === 0) {
    header('Location: /login.php');
    exit;
}

$controller = new OrderController();
$order = $controller->getOrder($userId, $orderId);

if ($order === false) {
    http_response_code(404);
    echo 'Order not found.';
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<?php
$pageTitle = 'Order #' . (int) $order['id'] . ' | Online Book Store';
$pageStyles = ['/assets/css/checkout.css'];
?>
<?php include __DIR__ . '/../views/layouts/head.php'; ?>
<body>
    <?php include __DIR__ . '/../views/layouts/flash.php'; ?>
    <div class="page-shell">
        <?php include __DIR__ . '/../views/layouts/header.php'; ?>

        <section class="checkout-page-head">
            <h1>Order #<?= (int) $order['id']; ?></h1>
            <p class="checkout-copy">Placed on <?= date('M j, Y', strtotime($order['created_at'])); ?> · Status: <?= htmlspecialchars($order['status'], ENT_QUOTES, 'UTF-8'); ?></p>
        </section>

        <section class="checkout-grid">
            <div class="checkout-form-shell">
                <h2>Shipping information</h2>
                <p><strong>Shipping address:</strong> <?= htmlspecialchars($order['shipping_address'], ENT_QUOTES, 'UTF-8'); ?></p>
                <p><strong>Phone:</strong> <?= htmlspecialchars($order['phone'], ENT_QUOTES, 'UTF-8'); ?></p>
                <p><strong>Payment method:</strong> <?= htmlspecialchars($order['payment_method'], ENT_QUOTES, 'UTF-8'); ?></p>
            </div>

            <aside class="order-summary-shell">
                <div class="order-summary-card">
                    <h2>Order total</h2>
                    <div class="summary-footer">
                        <span>Total</span>
                        <span>$<?= number_format((float) $order['total_amount'], 2); ?></span>
                    </div>
                </div>
            </aside>
        </section>

        <section class="orders-list">
            <?php foreach ($order['items'] as $item): ?>
                <article class="order-card">
                    <div>
                        <h2><?= htmlspecialchars($item['title'], ENT_QUOTES, 'UTF-8'); ?></h2>
                        <p class="book-meta">Qty: <?= (int) $item['quantity']; ?> · $<?= number_format((float) $item['price'], 2); ?> each</p>
                    </div>
                    <div class="order-meta">
                        <p>Subtotal</p>
                        <p>$<?= number_format((float) $item['subtotal'], 2); ?></p>
                    </div>
                </article>
            <?php endforeach; ?>
        </section>

        <?php include __DIR__ . '/../views/layouts/footer.php'; ?>
    </div>
    <?php include __DIR__ . '/../views/layouts/scripts.php'; ?>
</body>
</html>
