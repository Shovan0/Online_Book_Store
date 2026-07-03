<?php

declare(strict_types=1);

use App\Controllers\BookController;

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../app/Config/config.php';

$controller = new BookController();
$categories = $controller->getCategories();
$result = $controller->getBooks($_GET);
$books = $result['books'];
$total = $result['total'];
$page = $result['page'];
$limit = $result['limit'];
$totalPages = (int) ceil($total / $limit);
$search = trim((string) ($_GET['search'] ?? ''));
$selectedCategory = isset($_GET['category']) ? (int) $_GET['category'] : 0;
?>

<!DOCTYPE html>
<html lang="en">
<?php
$pageTitle = 'Books | Online Book Store';
$pageStyles = ['/assets/css/books.css'];
?>
<?php include __DIR__ . '/../views/layouts/head.php'; ?>
<body>
    <?php include __DIR__ . '/../views/layouts/flash.php'; ?>
    <div class="page-shell">
        <?php include __DIR__ . '/../views/layouts/header.php'; ?>

        <section class="page-heading">
            <h2>Explore books</h2>
            <p class="book-meta">Browse all titles, filter by category, and search by author or title.</p>
        </section>

        <section class="search-panel">
            <form action="/books.php" method="get">
                <div class="search-row">
                    <input class="search-input" type="search" name="search" value="<?= htmlspecialchars($search, ENT_QUOTES, 'UTF-8'); ?>" placeholder="Search by title or author">
                    <select class="select-input" name="category">
                        <option value="0">All categories</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?= (int) $category['id']; ?>" <?= $selectedCategory === (int) $category['id'] ? 'selected' : ''; ?>>
                                <?= htmlspecialchars($category['name'], ENT_QUOTES, 'UTF-8'); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <button class="filter-button" type="submit">Apply</button>
                </div>
            </form>
        </section>

        <section class="book-grid">
            <?php if (empty($books)): ?>
                <p class="book-meta">No books match your search or category filter.</p>
            <?php endif; ?>

            <?php foreach ($books as $book): ?>
                <article class="book-card">
                    <img class="book-cover" src="<?= htmlspecialchars($book['cover_image'], ENT_QUOTES, 'UTF-8'); ?>" alt="<?= htmlspecialchars($book['title'], ENT_QUOTES, 'UTF-8'); ?> cover">
                    <div class="book-card-body">
                        <p class="detail-badge"><?= htmlspecialchars($book['category'] ?? 'Uncategorized', ENT_QUOTES, 'UTF-8'); ?></p>
                        <h3 class="book-title"><?= htmlspecialchars($book['title'], ENT_QUOTES, 'UTF-8'); ?></h3>
                        <p class="book-meta">By <?= htmlspecialchars($book['author'], ENT_QUOTES, 'UTF-8'); ?></p>
                        <p class="book-price">$<?= number_format((float) $book['price'], 2); ?></p>
                        <a class="secondary-button" href="/book.php?id=<?= (int) $book['id']; ?>">View details</a>
                    </div>
                </article>
            <?php endforeach; ?>
        </section>

        <?php if ($totalPages > 1): ?>
            <section class="pagination">
                <?php for ($pageNumber = 1; $pageNumber <= $totalPages; $pageNumber++): ?>
                    <?php
                        $query = array_filter([
                            'search' => $search,
                            'category' => $selectedCategory > 0 ? $selectedCategory : null,
                            'page' => $pageNumber,
                        ], fn ($value) => $value !== null && $value !== '');
                        $url = '/books.php?' . http_build_query($query);
                    ?>
                    <a class="pagination-button <?= $pageNumber === $page ? 'active' : ''; ?>" href="<?= htmlspecialchars($url, ENT_QUOTES, 'UTF-8'); ?>"><?= $pageNumber; ?></a>
                <?php endfor; ?>
            </section>
        <?php endif; ?>

        <?php include __DIR__ . '/../views/layouts/footer.php'; ?>
    </div>
    <?php include __DIR__ . '/../views/layouts/scripts.php'; ?>
</body>
</html>
