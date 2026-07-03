<?php

declare(strict_types=1);

namespace App\Models;

use App\Config\Database;
use PDO;

class Category
{
    private PDO $connection;

    public function __construct()
    {
        $database = new Database();
        $this->connection = $database->getConnection();
    }

    public function getAll(): array
    {
        $query = 'SELECT id, name FROM categories ORDER BY name ASC';
        $statement = $this->connection->query($query);

        return $statement->fetchAll();
    }
}
