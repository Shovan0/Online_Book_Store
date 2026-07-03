<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../app/Config/config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (empty($_SESSION['user_id'])) {
    header('Location: /login.php');
    exit;
}

$userName = htmlspecialchars($_SESSION['user_name'] ?? 'Customer', ENT_QUOTES, 'UTF-8');
?>

<!DOCTYPE html>
<html lang="en">
<?php
$pageTitle = 'Dashboard | Online Book Store';
$pageStyles = [];
?>
<?php include __DIR__ . '/../views/layouts/head.php'; ?>
<body>
    <?php include __DIR__ . '/../views/layouts/flash.php'; ?>
    <main class="dashboard-shell">
        <h1>Welcome, <?= $userName; ?>!</h1>
        <p>You are logged in and ready to browse books, manage your cart, and place orders.</p>
        <a class="dashboard-action" href="/logout.php">Logout</a>
    </main>
    <?php include __DIR__ . '/../views/layouts/scripts.php'; ?>
</body>
</html>
