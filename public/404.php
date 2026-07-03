<?php

http_response_code(404);
?>

<!DOCTYPE html>
<html lang="en">
<?php
$pageTitle = 'Page Not Found | Online Book Store';
$pageStyles = ['/assets/css/style.css'];
?>
<?php include __DIR__ . '/../views/layouts/head.php'; ?>
<body>
    <?php include __DIR__ . '/../views/layouts/flash.php'; ?>
    <div class="page-shell">
        <?php include __DIR__ . '/../views/layouts/header.php'; ?>

        <main class="empty-state">
            <h2>Page not found</h2>
            <p>The page you’re looking for doesn’t exist or may have been moved.</p>
            <a class="button" href="/">Return home</a>
        </main>

        <?php include __DIR__ . '/../views/layouts/footer.php'; ?>
    </div>
    <?php include __DIR__ . '/../views/layouts/scripts.php'; ?>
</body>
</html>
