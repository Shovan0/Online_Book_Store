<?php

declare(strict_types=1);

namespace App\Helpers;

class Validation
{
    public static function sanitizeString(string $value): string
    {
        return trim($value);
    }

    public static function validateRequired(string $value): bool
    {
        return $value !== '';
    }

    public static function validateEmail(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    public static function validatePasswordLength(string $password, int $minimumLength = 8): bool
    {
        return mb_strlen($password) >= $minimumLength;
    }
}
