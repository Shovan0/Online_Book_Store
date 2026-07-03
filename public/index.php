<?php

declare(strict_types=1);

use App\Controllers\BookController;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../app/Config/config.php';

$controller = new BookController();
$featuredBooks = $controller->getFeaturedBooks();
$latestBooks = $controller->getLatestBooks();
?>

<!DOCTYPE html>
<html lang="en">
<?php
$pageTitle = 'Online Book Store';
$pageStyles = ['/assets/css/books.css'];
?>
<?php include __DIR__ . '/../views/layouts/head.php'; ?>
<body>
    <?php include __DIR__ . '/../views/layouts/flash.php'; ?>
    <div class="page-shell">
        <?php include __DIR__ . '/../views/layouts/header.php'; ?>

        <section class="hero-grid">
            <div class="hero-copy">
                <p class="detail-badge">Discover next reads</p>
                <h1>Find your next favorite story with curated recommendations.</h1>
                <p>Search by author, browse categories, and explore featured books for every reader.</p>
                <div class="hero-actions">
                    <a class="hero-button" href="/books.php">Browse books</a>
                    <a class="secondary-button" href="/register.php">Create account</a>
                </div>
            </div>
            <div class="hero-visual" aria-hidden="true">
                <div class="hero-panel">
                    <div class="hero-card">
                        <span class="detail-badge">New release</span>
                        <h2>Modern storytelling</h2>
                        <p>Explore bestselling narratives and compelling nonfiction from top authors.</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="search-panel">
            <form action="/books.php" method="get">
                <div class="search-row">
                    <input class="search-input" type="search" name="search" placeholder="Search by title or author">
                    <button class="filter-button" type="submit">Search</button>
                </div>
            </form>
        </section>

        <section>
            <div class="page-heading">
                <h2>Featured books</h2>
                <a class="secondary-button" href="/books.php">View all</a>
            </div>
            <div class="feature-grid">
                <?php foreach ($featuredBooks as $book): ?>
                    <article class="feature-card">
                        <img class="book-cover" src="<?= htmlspecialchars($book['cover_image'], ENT_QUOTES, 'UTF-8'); ?>" alt="<?= htmlspecialchars($book['title'], ENT_QUOTES, 'UTF-8'); ?> cover">
                        <div>
                            <p class="detail-badge"><?= htmlspecialchars($book['category'] ?? 'Uncategorized', ENT_QUOTES, 'UTF-8'); ?></p>
                            <h3><?= htmlspecialchars($book['title'], ENT_QUOTES, 'UTF-8'); ?></h3>
                            <p class="book-meta">By <?= htmlspecialchars($book['author'], ENT_QUOTES, 'UTF-8'); ?></p>
                        </div>
                        <p class="book-price">$<?= number_format((float) $book['price'], 2); ?></p>
                        <div class="feature-actions">
                            <a class="secondary-button" href="/book.php?id=<?= (int) $book['id']; ?>">View details</a>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </section>

        <section style="margin-top:40px;">
            <div class="page-heading">
                <h2>Latest arrivals</h2>
            </div>
            <div class="book-grid">
                <?php foreach ($latestBooks as $book): ?>
                    <article class="book-card">
                        <img class="book-cover" src="<?= htmlspecialchars($book['cover_image'], ENT_QUOTES, 'UTF-8'); ?>" alt="<?= htmlspecialchars($book['title'], ENT_QUOTES, 'UTF-8'); ?> cover">
                        <div class="book-card-body">
                            <p class="detail-badge"><?= htmlspecialchars($book['category'] ?? 'Uncategorized', ENT_QUOTES, 'UTF-8'); ?></p>
                            <h3 class="book-title"><?= htmlspecialchars($book['title'], ENT_QUOTES, 'UTF-8'); ?></h3>
                            <p class="book-meta">By <?= htmlspecialchars($book['author'], ENT_QUOTES, 'UTF-8'); ?></p>
                            <p class="book-price">$<?= number_format((float) $book['price'], 2); ?></p>

                            <form class="cart-action" method="post" action="/cart.php">
                                <input type="hidden" name="action" value="add">
                                <input type="hidden" name="book_id" value="<?= (int) $book['id']; ?>">
                                <input type="hidden" name="quantity" value="1">
                                <button class="add-button" type="submit">Add to cart</button>
                            </form>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </section>

        <?php include __DIR__ . '/../views/layouts/footer.php'; ?>
    </div>
    <?php include __DIR__ . '/../views/layouts/scripts.php'; ?>
</body>
</html>
