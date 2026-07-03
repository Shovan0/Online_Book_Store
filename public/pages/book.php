<?php

declare(strict_types=1);

use App\Controllers\BookController;

require_once dirname(__DIR__, 2) . '/vendor/autoload.php';
require_once dirname(__DIR__, 2) . '/app/Config/config.php';

$controller = new BookController();
$bookId = isset($_GET['id']) && is_numeric($_GET['id']) ? (int) $_GET['id'] : 0;
$book = $controller->getBook($bookId);

if ($book === false) {
    http_response_code(404);
    echo 'Book not found.';
    exit;
}

// Defensive check: ensure returned record matches requested id
if (!isset($book['id']) || (int) $book['id'] !== $bookId) {
    // Possible stale or inconsistent data — treat as not found to avoid mismatch
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
<?php include dirname(__DIR__, 2) . '/views/layouts/head.php'; ?>
<body>
    <?php include dirname(__DIR__, 2) . '/views/layouts/flash.php'; ?>
    <div class="page-shell">
        <?php include dirname(__DIR__, 2) . '/views/layouts/header.php'; ?>

        <section class="book-detail-shell">
            <div class="image-card card">
                <img class="detail-cover" src="<?= htmlspecialchars($book['cover_image'] ?? '/assets/images/placeholder.png', ENT_QUOTES, 'UTF-8'); ?>" alt="<?= htmlspecialchars($book['title'], ENT_QUOTES, 'UTF-8'); ?> cover">
            </div>
            <div class="detail-info card">
                <div class="detail-top">
                    <span class="detail-badge"><?= htmlspecialchars($book['category'] ?? 'Uncategorized', ENT_QUOTES, 'UTF-8'); ?></span>
                    <h1><?= htmlspecialchars($book['title'], ENT_QUOTES, 'UTF-8'); ?></h1>
                    <p class="book-meta">By <?= htmlspecialchars($book['author'], ENT_QUOTES, 'UTF-8'); ?></p>
                </div>

                <div class="detail-main">
                    <p class="detail-price">$<?= number_format((float) $book['price'], 2); ?></p>
                    <p class="detail-stock"><?= (int) $book['stock'] > 0 ? 'In stock' : 'Out of stock'; ?></p>
                    <p class="detail-meta"><?= nl2br(htmlspecialchars($book['description'] ?? 'No description available.', ENT_QUOTES, 'UTF-8')); ?></p>
                </div>

                <form class="detail-form" method="post" action="/cart.php">
                    <input type="hidden" name="action" value="add">
                    <input type="hidden" name="book_id" value="<?= (int) $book['id']; ?>">
                    <div class="input-group">
                        <label class="input-label" for="quantity">Quantity</label>
                        <input class="input-field" type="number" id="quantity" name="quantity" value="1" min="1" step="1" required max="<?= (int) $book['stock']; ?>">
                    </div>
                    <div>
                        <button class="detail-button" type="submit" style="width:100%;" <?= (int) $book['stock'] === 0 ? 'disabled' : ''; ?>>Add to Cart</button>
                    </div>
                </form>
            </div>
        </section>

        <?php include dirname(__DIR__, 2) . '/views/layouts/footer.php'; ?>
    </div>
    <?php include dirname(__DIR__, 2) . '/views/layouts/scripts.php'; ?>
</body>
</html>
