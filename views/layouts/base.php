<?php
$pageTitle = $pageTitle ?? 'Online Book Store';
$pageStyles = $pageStyles ?? [];
?>
<!DOCTYPE html>
<html lang="en">
<?php include __DIR__ . '/head.php'; ?>
<body>
    <?php include __DIR__ . '/flash.php'; ?>
    <?= $content; ?>
    <?php include __DIR__ . '/scripts.php'; ?>
</body>
</html>
