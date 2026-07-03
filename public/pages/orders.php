<?php

declare(strict_types=1);

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

$controller = new OrderController();
$orders = $controller->getOrders($userId);
?>

<!DOCTYPE html>
<html lang="en">
<?php
$pageTitle = 'Order History | Online Book Store';
$pageStyles = ['/assets/css/checkout.css'];
?>
<?php include dirname(__DIR__, 2) . '/views/layouts/head.php'; ?>
<body>
    <?php include dirname(__DIR__, 2) . '/views/layouts/flash.php'; ?>
    <div class="page-shell">
        <?php include dirname(__DIR__, 2) . '/views/layouts/header.php'; ?>

        <section class="checkout-page-head">
            <h1>Order history</h1>
            <p class="checkout-copy">Review your past orders and check current order status.</p>
        </section>

        <?php if (empty($orders)): ?>
            <div class="empty-state">
                <p>You do not have any orders yet.</p>
                <a class="empty-button" href="/books.php">Start shopping</a>
            </div>
        <?php else: ?>
            <div class="orders-list">
                <?php foreach ($orders as $order): ?>
                    <?php $itemsCount = isset($order['items']) ? count($order['items']) : ($order['item_count'] ?? null); ?>
                    <article class="order-card card">
                        <div class="order-card-left">
                            <div>
                                <h3>Order #<?= (int) $order['id']; ?></h3>
                                <p class="book-meta">Placed on <?= date('M j, Y', strtotime($order['created_at'])); ?></p>
                                <?php if ($itemsCount !== null): ?>
                                    <p class="book-meta">Items: <?= (int) $itemsCount; ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="order-meta">
                            <p class="meta-label">Total</p>
                            <p class="meta-value">$<?= number_format((float) $order['total_amount'], 2); ?></p>
                            <span class="status-badge"><?= htmlspecialchars($order['status'], ENT_QUOTES, 'UTF-8'); ?></span>
                            <a class="secondary-button" href="/order.php?id=<?= (int) $order['id']; ?>">View details</a>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php include dirname(__DIR__, 2) . '/views/layouts/footer.php'; ?>
    </div>
    <?php include dirname(__DIR__, 2) . '/views/layouts/scripts.php'; ?>
</body>
</html>
