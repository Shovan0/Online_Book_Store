<?php

declare(strict_types=1);

namespace App\Helpers;

class Flash
{
    public static function startSession(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function set(string $type, string $message): void
    {
        self::startSession();

        if (!isset($_SESSION['flash_messages'])) {
            $_SESSION['flash_messages'] = [];
        }

        $_SESSION['flash_messages'][] = ['type' => $type, 'message' => $message];
    }

    public static function getAll(): array
    {
        self::startSession();

        $messages = $_SESSION['flash_messages'] ?? [];
        unset($_SESSION['flash_messages']);

        return $messages;
    }
}
