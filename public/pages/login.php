<?php

declare(strict_types=1);

use App\Controllers\AuthController;

require_once dirname(__DIR__, 2) . '/vendor/autoload.php';
require_once dirname(__DIR__, 2) . '/app/Config/config.php';

$controller = new AuthController();
$errors = [];
$values = ['email' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = $controller->login($_POST);

    if (isset($result['errors'])) {
        $errors = $result['errors'];
        $values = array_merge($values, $result['values']);
    } else {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $_SESSION['user_id'] = $result['user']['id'];
        $_SESSION['user_name'] = $result['user']['name'];

        header('Location: /index.php');
        exit;
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<?php
$pageTitle = 'Login | Online Book Store';
$pageStyles = ['/assets/css/auth.css'];
?>
<?php include dirname(__DIR__, 2) . '/views/layouts/head.php'; ?>
<body>
    <?php include dirname(__DIR__, 2) . '/views/layouts/flash.php'; ?>
    <a class="auth-home-link" href="/index.php">Online Book Store</a>
    <main class="auth-shell">
        <header class="auth-header">
            <h1 class="auth-title">Welcome back</h1>
            <p class="auth-copy">Log in to access your account and start shopping.</p>
        </header>

        <form class="auth-form" method="post" action="" novalidate>
            <div class="input-group">
                <label class="input-label" for="email">Email</label>
                <input class="input-field" type="email" id="email" name="email" value="<?= htmlspecialchars($values['email'], ENT_QUOTES, 'UTF-8'); ?>" required>
                <?php if (!empty($errors['email'])): ?>
                    <div class="field-error"><?= htmlspecialchars($errors['email'], ENT_QUOTES, 'UTF-8'); ?></div>
                <?php endif; ?>
            </div>

            <div class="input-group">
                <label class="input-label" for="password">Password</label>
                <div class="password-field-wrap">
                    <input class="input-field" type="password" id="password" name="password" required>
                    <button class="password-toggle" type="button" data-target="password">Show</button>
                </div>
                <?php if (!empty($errors['password'])): ?>
                    <div class="field-error"><?= htmlspecialchars($errors['password'], ENT_QUOTES, 'UTF-8'); ?></div>
                <?php endif; ?>
            </div>

            <?php if (!empty($errors['credentials'])): ?>
                <div class="field-error"><?= htmlspecialchars($errors['credentials'], ENT_QUOTES, 'UTF-8'); ?></div>
            <?php endif; ?>

            <button class="input-button" type="submit">Login</button>
        </form>

        <p class="auth-footer">Don’t have an account? <a class="form-link" href="register.php">Register</a>.</p>
    </main>
    <?php include dirname(__DIR__, 2) . '/views/layouts/scripts.php'; ?>
    <script>
        document.querySelectorAll('.password-toggle').forEach((button) => {
            button.addEventListener('click', () => {
                const targetId = button.getAttribute('data-target');
                const input = document.getElementById(targetId);
                if (!input) {
                    return;
                }

                const isPassword = input.type === 'password';
                input.type = isPassword ? 'text' : 'password';
                button.textContent = isPassword ? 'Hide' : 'Show';
            });
        });
    </script>
</body>
</html>
