<?php

declare(strict_types=1);

use App\Controllers\AuthController;

require_once dirname(__DIR__, 2) . '/vendor/autoload.php';
require_once dirname(__DIR__, 2) . '/app/Config/config.php';

$controller = new AuthController();
$errors = [];
$values = ['name' => '', 'email' => ''];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = $controller->register($_POST);

    if (isset($result['errors'])) {
        $errors = $result['errors'];
        $values = array_merge($values, $result['values']);
    } else {
        $success = true;
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<?php
$pageTitle = 'Register | Online Book Store';
$pageStyles = ['/assets/css/auth.css'];
?>
<?php include dirname(__DIR__, 2) . '/views/layouts/head.php'; ?>
<body>
    <?php include dirname(__DIR__, 2) . '/views/layouts/flash.php'; ?>
    <main class="auth-shell">
        <header class="auth-header">
            <h1 class="auth-title">Create your account</h1>
            <p class="auth-copy">Register and start shopping books with your secure account.</p>
        </header>

        <?php if ($success): ?>
            <p class="form-error">Your account has been created. <a class="form-link" href="login.php">Log in now</a>.</p>
        <?php endif; ?>

        <?php if (!$success): ?>
            <form class="auth-form" method="post" action="" novalidate>
                <div class="input-group">
                    <label class="input-label" for="name">Name</label>
                    <input class="input-field" type="text" id="name" name="name" value="<?= htmlspecialchars($values['name'], ENT_QUOTES, 'UTF-8'); ?>" required>
                    <?php if (!empty($errors['name'])): ?>
                        <div class="field-error"><?= htmlspecialchars($errors['name'], ENT_QUOTES, 'UTF-8'); ?></div>
                    <?php endif; ?>
                </div>

                <div class="input-group">
                    <label class="input-label" for="email">Email</label>
                    <input class="input-field" type="email" id="email" name="email" value="<?= htmlspecialchars($values['email'], ENT_QUOTES, 'UTF-8'); ?>" required>
                    <?php if (!empty($errors['email'])): ?>
                        <div class="field-error"><?= htmlspecialchars($errors['email'], ENT_QUOTES, 'UTF-8'); ?></div>
                    <?php endif; ?>
                </div>

                <div class="input-group">
                    <label class="input-label" for="password">Password</label>
                    <input class="input-field" type="password" id="password" name="password" required>
                    <?php if (!empty($errors['password'])): ?>
                        <div class="field-error"><?= htmlspecialchars($errors['password'], ENT_QUOTES, 'UTF-8'); ?></div>
                    <?php endif; ?>
                </div>

                <div class="input-group">
                    <label class="input-label" for="confirm_password">Confirm Password</label>
                    <input class="input-field" type="password" id="confirm_password" name="confirm_password" required>
                    <?php if (!empty($errors['confirm_password'])): ?>
                        <div class="field-error"><?= htmlspecialchars($errors['confirm_password'], ENT_QUOTES, 'UTF-8'); ?></div>
                    <?php endif; ?>
                </div>

                <button class="input-button" type="submit">Register</button>
            </form>

            <?php if (!empty($errors['credentials'])): ?>
                <p class="form-error"><?= htmlspecialchars($errors['credentials'], ENT_QUOTES, 'UTF-8'); ?></p>
            <?php endif; ?>

            <p class="auth-footer">Already have an account? <a class="form-link" href="login.php">Log in</a>.</p>
        <?php endif; ?>
    </main>
    <?php include dirname(__DIR__, 2) . '/views/layouts/scripts.php'; ?>
</html>
