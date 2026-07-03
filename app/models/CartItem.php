<?php

declare(strict_types=1);

namespace App\Models;

use App\Config\Database;
use PDO;

class CartItem
{
    private PDO $connection;

    public function __construct()
    {
        $database = new Database();
        $this->connection = $database->getConnection();
    }

    public function addOrUpdate(int $userId, int $bookId, int $quantity): void
    {
        $existing = $this->findByUserAndBook($userId, $bookId);

        if ($existing) {
            $newQuantity = max(1, $existing['quantity'] + $quantity);
            $query = 'UPDATE cart SET quantity = :quantity WHERE id = :id';
            $statement = $this->connection->prepare($query);
            $statement->bindValue(':quantity', $newQuantity, PDO::PARAM_INT);
            $statement->bindValue(':id', $existing['id'], PDO::PARAM_INT);
            $statement->execute();

            return;
        }

        $query = 'INSERT INTO cart (user_id, book_id, quantity, created_at) VALUES (:user_id, :book_id, :quantity, NOW())';
        $statement = $this->connection->prepare($query);
        $statement->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $statement->bindValue(':book_id', $bookId, PDO::PARAM_INT);
        $statement->bindValue(':quantity', max(1, $quantity), PDO::PARAM_INT);
        $statement->execute();
    }

    public function findByUserAndBook(int $userId, int $bookId): array|false
    {
        $query = 'SELECT id, quantity FROM cart WHERE user_id = :user_id AND book_id = :book_id LIMIT 1';
        $statement = $this->connection->prepare($query);
        $statement->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $statement->bindValue(':book_id', $bookId, PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetch();
    }

    public function getCartItems(int $userId): array
    {
        $query = <<<SQL
SELECT c.id,
       c.book_id,
       c.quantity,
       b.title,
       b.price,
       b.cover_image,
       b.stock
FROM cart c
JOIN books b ON b.id = c.book_id
WHERE c.user_id = :user_id
ORDER BY c.created_at DESC
SQL;

        $statement = $this->connection->prepare($query);
        $statement->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetchAll();
    }

    public function updateQuantity(int $cartId, int $quantity): void
    {
        $quantity = max(1, $quantity);
        $query = 'UPDATE cart SET quantity = :quantity WHERE id = :id';
        $statement = $this->connection->prepare($query);
        $statement->bindValue(':quantity', $quantity, PDO::PARAM_INT);
        $statement->bindValue(':id', $cartId, PDO::PARAM_INT);
        $statement->execute();
    }

    public function remove(int $cartId): void
    {
        $query = 'DELETE FROM cart WHERE id = :id';
        $statement = $this->connection->prepare($query);
        $statement->bindValue(':id', $cartId, PDO::PARAM_INT);
        $statement->execute();
    }

    public function emptyCart(int $userId): void
    {
        $query = 'DELETE FROM cart WHERE user_id = :user_id';
        $statement = $this->connection->prepare($query);
        $statement->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $statement->execute();
    }
}
