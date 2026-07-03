<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\User;

class AuthController
{
    private User $userModel;

    public function __construct()
    {
        $this->userModel = new User();
    }

    public function register(array $data): array
    {
        $errors = [];

        $name = trim($data['name'] ?? '');
        $email = trim($data['email'] ?? '');
        $password = $data['password'] ?? '';
        $confirmPassword = $data['confirm_password'] ?? '';

        if ($name === '') {
            $errors['name'] = 'Name is required.';
        }

        if ($email === '') {
            $errors['email'] = 'Email is required.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Please enter a valid email address.';
        }

        if ($password === '') {
            $errors['password'] = 'Password is required.';
        } elseif (mb_strlen($password) < 8) {
            $errors['password'] = 'Password must be at least 8 characters.';
        }

        if ($confirmPassword === '') {
            $errors['confirm_password'] = 'Confirm password is required.';
        } elseif ($password !== $confirmPassword) {
            $errors['confirm_password'] = 'Passwords do not match.';
        }

        if (empty($errors) && $this->userModel->findByEmail($email)) {
            $errors['email'] = 'An account with this email already exists.';
        }

        if (!empty($errors)) {
            return ['errors' => $errors, 'values' => ['name' => $name, 'email' => $email]];
        }

        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        $userId = $this->userModel->create($name, $email, $passwordHash);

        return ['user_id' => $userId, 'name' => $name, 'email' => $email];
    }

    public function login(array $data): array
    {
        $errors = [];

        $email = trim($data['email'] ?? '');
        $password = $data['password'] ?? '';

        if ($email === '') {
            $errors['email'] = 'Email is required.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Please enter a valid email address.';
        }

        if ($password === '') {
            $errors['password'] = 'Password is required.';
        }

        if (!empty($errors)) {
            return ['errors' => $errors, 'values' => ['email' => $email]];
        }

        $user = $this->userModel->findByEmail($email);

        if ($user === false || !password_verify($password, (string) $user['password'])) {
            $errors['credentials'] = 'Email or password is incorrect.';
            return ['errors' => $errors, 'values' => ['email' => $email]];
        }

        return ['user' => $user];
    }
}
