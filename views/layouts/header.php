<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$currentPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$links = [
    ['href' => '/', 'label' => 'Home'],
    ['href' => '/books.php', 'label' => 'Books'],
    ['href' => '/cart.php', 'label' => 'Cart'],
];

if (!empty($_SESSION['user_id'])) {
    $links[] = ['href' => '/orders.php', 'label' => 'Orders'];
    $links[] = ['href' => '/dashboard.php', 'label' => 'Profile'];
    $links[] = ['href' => '/logout.php', 'label' => 'Logout'];
} else {
    $links[] = ['href' => '/login.php', 'label' => 'Login'];
    $links[] = ['href' => '/register.php', 'label' => 'Register'];
}
?>

<header class="site-header">
    <div class="site-brand">
        <a class="brand-link" href="/">Online Book Store</a>
    </div>
    <nav class="site-nav" aria-label="Main navigation">
        <?php foreach ($links as $link): ?>
            <a class="nav-link <?= $currentPath === $link['href'] ? 'active' : ''; ?>" href="<?= htmlspecialchars($link['href'], ENT_QUOTES, 'UTF-8'); ?>"><?= htmlspecialchars($link['label'], ENT_QUOTES, 'UTF-8'); ?></a>
        <?php endforeach; ?>
    </nav>
</header>
