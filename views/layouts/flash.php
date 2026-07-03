<?php

use App\Helpers\Flash;

$flashMessages = Flash::getAll();
?>

<?php if (!empty($flashMessages)): ?>
    <div class="toast-container">
        <?php foreach ($flashMessages as $message): ?>
            <div class="toast <?= htmlspecialchars($message['type'], ENT_QUOTES, 'UTF-8'); ?>">
                <?= htmlspecialchars($message['message'], ENT_QUOTES, 'UTF-8'); ?>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
