<?php

declare(strict_types=1);

use App\Controllers\OrderController;

require_once dirname(__DIR__, 2) . '/vendor/autoload.php';
require_once dirname(__DIR__, 2) . '/app/Config/config.php';

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
<?php include dirname(__DIR__, 2) . '/views/layouts/head.php'; ?>
<body>
    <?php include dirname(__DIR__, 2) . '/views/layouts/flash.php'; ?>
    <div class="page-shell">
        <?php include dirname(__DIR__, 2) . '/views/layouts/header.php'; ?>

        <section class="checkout-page-head">
            <h1>Order #<?= (int) $order['id']; ?></h1>
            <p class="checkout-copy">Placed on <?= date('M j, Y', strtotime($order['created_at'])); ?> · Status: <?= htmlspecialchars($order['status'], ENT_QUOTES, 'UTF-8'); ?></p>
        </section>

        <section class="checkout-grid">
            <div class="checkout-form-shell card">
                <h2>Shipping information</h2>
                <div class="shipping-info">
                    <p><strong>Shipping address:</strong> <?= htmlspecialchars($order['shipping_address'], ENT_QUOTES, 'UTF-8'); ?></p>
                    <p><strong>Phone:</strong> <?= htmlspecialchars($order['phone'], ENT_QUOTES, 'UTF-8'); ?></p>
                    <p><strong>Payment method:</strong> <?= htmlspecialchars($order['payment_method'], ENT_QUOTES, 'UTF-8'); ?></p>
                </div>
            </div>

            <aside class="order-summary-shell">
                <div class="order-summary-card card">
                    <h2>Order total</h2>
                    <div class="summary-footer">
                        <p class="summary-label">Total</p>
                        <p class="summary-amount">$<?= number_format((float) $order['total_amount'], 2); ?></p>
                    </div>
                </div>
            </aside>
        </section>

        <section class="orders-list">
            <?php foreach ($order['items'] as $item): ?>
                <article class="order-card card">
                    <div class="order-card-left">
                        <img src="<?= htmlspecialchars($item['cover_image'] ?? '/assets/images/placeholder.png', ENT_QUOTES, 'UTF-8'); ?>" alt="<?= htmlspecialchars($item['title'], ENT_QUOTES, 'UTF-8'); ?>" class="order-item-image">
                        <div class="order-item-info">
                            <h3><?= htmlspecialchars($item['title'], ENT_QUOTES, 'UTF-8'); ?></h3>
                            <p class="book-meta">Qty: <?= (int) $item['quantity']; ?> · $<?= number_format((float) $item['price'], 2); ?> each</p>
                        </div>
                    </div>
                    <div class="order-meta">
                        <p class="meta-label">Subtotal</p>
                        <p class="meta-value">$<?= number_format((float) $item['subtotal'], 2); ?></p>
                    </div>
                </article>
            <?php endforeach; ?>
        </section>

        <?php include dirname(__DIR__, 2) . '/views/layouts/footer.php'; ?>
    </div>
    <?php include dirname(__DIR__, 2) . '/views/layouts/scripts.php'; ?>
</body>
</html>
