<?php

declare(strict_types=1);

namespace App\Models;

use App\Config\Database;
use PDO;
use PDOException;

class User
{
    private PDO $connection;

    public function __construct()
    {
        $database = new Database();
        $this->connection = $database->getConnection();
    }

    public function findByEmail(string $email): array|false
    {
        $query = 'SELECT id, full_name AS name, email, password, created_at FROM users WHERE email = :email LIMIT 1';
        $statement = $this->connection->prepare($query);
        $statement->bindValue(':email', $email, PDO::PARAM_STR);
        $statement->execute();

        return $statement->fetch();
    }

    public function create(string $name, string $email, string $password): int
    {
        $query = 'INSERT INTO users (full_name, email, password, created_at) VALUES (:name, :email, :password, NOW())';
        $statement = $this->connection->prepare($query);
        $statement->bindValue(':name', $name, PDO::PARAM_STR);
        $statement->bindValue(':email', $email, PDO::PARAM_STR);
        $statement->bindValue(':password', $password, PDO::PARAM_STR);
        $statement->execute();

        return (int) $this->connection->lastInsertId('users_id_seq');
    }
}
