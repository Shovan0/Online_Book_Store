<?php

declare(strict_types=1);

namespace App\Middleware;

class AuthMiddleware
{
    public static function ensureGuest(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!empty($_SESSION['user_id'])) {
            header('Location: /dashboard.php');
            exit;
        }
    }

    public static function ensureAuthenticated(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (empty($_SESSION['user_id'])) {
            header('Location: /login.php');
            exit;
        }
    }
}
