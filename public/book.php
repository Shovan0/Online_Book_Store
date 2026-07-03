<?php

declare(strict_types=1);

use App\Controllers\BookController;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../app/Config/config.php';

$controller = new BookController();
$bookId = isset($_GET['id']) && is_numeric($_GET['id']) ? (int) $_GET['id'] : 0;
$book = $controller->getBook($bookId);

if ($book === false) {
    http_response_code(404);
    echo 'Book not found.';
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<?php
$pageTitle = htmlspecialchars($book['title'], ENT_QUOTES, 'UTF-8') . ' | Online Book Store';
$pageStyles = ['/assets/css/books.css'];
?>
<?php include __DIR__ . '/../views/layouts/head.php'; ?>
<body>
    <?php include __DIR__ . '/../views/layouts/flash.php'; ?>
    <div class="page-shell">
        <?php include __DIR__ . '/../views/layouts/header.php'; ?>

        <section class="book-detail-shell">
            <div>
                <img class="detail-cover" src="<?= htmlspecialchars($book['cover_image'], ENT_QUOTES, 'UTF-8'); ?>" alt="<?= htmlspecialchars($book['title'], ENT_QUOTES, 'UTF-8'); ?> cover">
            </div>
            <div class="detail-info">
                <span class="detail-badge"><?= htmlspecialchars($book['category'] ?? 'Uncategorized', ENT_QUOTES, 'UTF-8'); ?></span>
                <h1><?= htmlspecialchars($book['title'], ENT_QUOTES, 'UTF-8'); ?></h1>
                <p class="book-meta">By <?= htmlspecialchars($book['author'], ENT_QUOTES, 'UTF-8'); ?></p>
                <p class="detail-price">$<?= number_format((float) $book['price'], 2); ?></p>
                <p class="detail-stock"><?= (int) $book['stock'] > 0 ? 'In stock' : 'Out of stock'; ?></p>
                <p class="detail-meta"><?= nl2br(htmlspecialchars($book['description'] ?? 'No description available.', ENT_QUOTES, 'UTF-8')); ?></p>
                <form method="post" action="/cart.php" style="display:grid; gap:16px; max-width:320px;">
                    <input type="hidden" name="action" value="add">
                    <input type="hidden" name="book_id" value="<?= (int) $book['id']; ?>">
                    <label for="quantity">Quantity</label>
                    <input class="quantity-input" type="number" id="quantity" name="quantity" value="1" min="1" max="<?= (int) $book['stock']; ?>">
                    <button class="detail-button" type="submit" <?= (int) $book['stock'] === 0 ? 'disabled' : ''; ?>>Add to Cart</button>
                </form>
            </div>
        </section>

        <?php include __DIR__ . '/../views/layouts/footer.php'; ?>
    </div>
    <?php include __DIR__ . '/../views/layouts/scripts.php'; ?>
</body>
</html>
