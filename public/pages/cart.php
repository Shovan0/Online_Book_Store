<?php

declare(strict_types=1);

use App\Controllers\CartController;
use App\Helpers\Flash;

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

$controller = new CartController();
$action = $_POST['action'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if ($action === 'add' && isset($_POST['book_id'], $_POST['quantity'])) {
            $controller->add($userId, (int) $_POST['book_id'], max(1, (int) $_POST['quantity']));
            Flash::set('success', 'Book added to your cart.');
        }

        if ($action === 'update' && isset($_POST['cart_id'], $_POST['quantity'])) {
            $controller->update((int) $_POST['cart_id'], (int) $_POST['quantity']);
            Flash::set('success', 'Cart quantity updated.');
        }

        if ($action === 'remove' && isset($_POST['cart_id'])) {
            $controller->remove((int) $_POST['cart_id']);
            Flash::set('success', 'Item removed from your cart.');
        }

        if ($action === 'empty') {
            $controller->empty($userId);
            Flash::set('success', 'Your cart has been emptied.');
        }

        header('Location: /cart.php');
        exit;
    }
$items = $controller->list($userId);
$grandTotal = array_reduce($items, static fn ($sum, $item) => $sum + ((float) $item['price'] * (int) $item['quantity']), 0.0);
?>

<!DOCTYPE html>
<html lang="en">
<?php
$pageTitle = 'Cart | Online Book Store';
$pageStyles = ['/assets/css/cart.css'];
?>
<?php include dirname(__DIR__, 2) . '/views/layouts/head.php'; ?>
<body>
    <?php include dirname(__DIR__, 2) . '/views/layouts/flash.php'; ?>
    <div class="page-shell">
        <?php include dirname(__DIR__, 2) . '/views/layouts/header.php'; ?>

        <section class="page-heading">
            <h2>Your cart</h2>
            <form method="post" action="/cart.php" style="margin:0;">
                <input type="hidden" name="action" value="empty">
                <button class="empty-button" type="submit" <?= empty($items) ? 'disabled' : ''; ?>>Empty cart</button>
            </form>
        </section>

        <?php if (empty($items)): ?>
            <div class="empty-state">
                <p>Your cart is empty right now.</p>
                <a href="/books.php">Browse books</a>
            </div>
        <?php else: ?>
            <table class="cart-table">
                <thead>
                    <tr>
                        <th>Book</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Subtotal</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $item): ?>
                        <tr>
                            <td>
                                <div class="cart-item">
                                    <img class="cart-image" src="<?= htmlspecialchars($item['cover_image'], ENT_QUOTES, 'UTF-8'); ?>" alt="<?= htmlspecialchars($item['title'], ENT_QUOTES, 'UTF-8'); ?>">
                                    <div>
                                        <p class="cart-title"><?= htmlspecialchars($item['title'], ENT_QUOTES, 'UTF-8'); ?></p>
                                        <p class="cart-author">Stock: <?= (int) $item['stock']; ?></p>
                                    </div>
                                </div>
                            </td>
                            <td><p class="cart-price">$<?= number_format((float) $item['price'], 2); ?></p></td>
                            <td>
                                <form class="quantity-form" method="post" action="/cart.php">
                                    <input type="hidden" name="action" value="update">
                                    <input type="hidden" name="cart_id" value="<?= (int) $item['id']; ?>">
                                    <input class="quantity-input" type="number" name="quantity" value="<?= (int) $item['quantity']; ?>" min="1">
                                    <button class="secondary-button" type="submit">Update</button>
                                </form>
                            </td>
                            <td><p class="cart-subtotal">$<?= number_format((float) $item['price'] * (int) $item['quantity'], 2); ?></p></td>
                            <td>
                                <form method="post" action="/cart.php">
                                    <input type="hidden" name="action" value="remove">
                                    <input type="hidden" name="cart_id" value="<?= (int) $item['id']; ?>">
                                    <button class="remove-button" type="submit">Remove</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <section class="cart-summary">
                <div class="summary-row">
                    <span>Grand total</span>
                    <span>$<?= number_format($grandTotal, 2); ?></span>
                </div>
                <div class="summary-row total">
                    <span>Total</span>
                    <span>$<?= number_format($grandTotal, 2); ?></span>
                </div>
                <a class="checkout-button" href="/checkout.php">Place Order</a>
            </section>
        <?php endif; ?>

        <?php include dirname(__DIR__, 2) . '/views/layouts/footer.php'; ?>
    </div>
    <?php include dirname(__DIR__, 2) . '/views/layouts/scripts.php'; ?>
</body>
</html>
