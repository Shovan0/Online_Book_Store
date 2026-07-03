<?php

declare(strict_types=1);

namespace App\Models;

use App\Config\Database;
use PDO;

class Book
{
    private PDO $connection;

    public function __construct()
    {
        $database = new Database();
        $this->connection = $database->getConnection();
    }

    public function getFeatured(int $limit = 4): array
    {
        $query = <<<SQL
SELECT b.id,
       b.title,
       b.author,
       b.price,
       b.cover_image,
       c.name AS category
FROM books b
LEFT JOIN categories c ON c.id = b.category_id
ORDER BY b.created_at DESC
LIMIT :limit
SQL;
        $statement = $this->connection->prepare($query);
        $statement->bindValue(':limit', $limit, PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetchAll();
    }

    public function getLatest(int $limit = 6): array
    {
        $query = <<<SQL
SELECT b.id,
       b.title,
       b.author,
       b.price,
       b.cover_image,
       c.name AS category
FROM books b
LEFT JOIN categories c ON c.id = b.category_id
ORDER BY b.created_at DESC
LIMIT :limit
SQL;
        $statement = $this->connection->prepare($query);
        $statement->bindValue(':limit', $limit, PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetchAll();
    }

    public function search(array $criteria, int $page, int $limit): array
    {
        $search = trim((string) ($criteria['search'] ?? ''));
        $categoryId = isset($criteria['category']) ? (int) $criteria['category'] : 0;
        $offset = ($page - 1) * $limit;

        $conditions = ['1=1'];
        $bindings = [];

        if ($search !== '') {
            $conditions[] = '(b.title ILIKE :search OR b.author ILIKE :search)';
            $bindings[':search'] = '%' . $search . '%';
        }

        if ($categoryId > 0) {
            $conditions[] = 'b.category_id = :category_id';
            $bindings[':category_id'] = $categoryId;
        }

        $where = implode(' AND ', $conditions);

        $query = <<<SQL
SELECT b.id,
       b.title,
       b.author,
       b.price,
       b.cover_image,
       b.stock,
       c.name AS category
FROM books b
LEFT JOIN categories c ON c.id = b.category_id
WHERE {$where}
ORDER BY b.created_at DESC
LIMIT :limit OFFSET :offset
SQL;

        $statement = $this->connection->prepare($query);

        foreach ($bindings as $key => $value) {
            $statement->bindValue($key, $value, PDO::PARAM_STR);
        }

        $statement->bindValue(':limit', $limit, PDO::PARAM_INT);
        $statement->bindValue(':offset', $offset, PDO::PARAM_INT);
        $statement->execute();

        $books = $statement->fetchAll();

        $countQuery = "SELECT COUNT(*) AS total FROM books b LEFT JOIN categories c ON c.id = b.category_id WHERE {$where}";
        $countStatement = $this->connection->prepare($countQuery);

        foreach ($bindings as $key => $value) {
            $countStatement->bindValue($key, $value, PDO::PARAM_STR);
        }

        $countStatement->execute();
        $total = (int) $countStatement->fetchColumn();

        return ['books' => $books, 'total' => $total];
    }

    public function findById(int $id): array|false
    {
        $query = <<<SQL
SELECT b.id,
       b.title,
       b.author,
       b.description,
       b.price,
       b.stock,
       b.cover_image,
       c.name AS category
FROM books b
LEFT JOIN categories c ON c.id = b.category_id
WHERE b.id = :id
LIMIT 1
SQL;

        $statement = $this->connection->prepare($query);
        $statement->bindValue(':id', $id, PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetch();
    }
}
