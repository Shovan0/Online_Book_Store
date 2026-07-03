<?php

declare(strict_types=1);

namespace App\Models;

use App\Config\Database;
use PDO;
use PDOException;

class Order
{
    private PDO $connection;

    public function __construct()
    {
        $database = new Database();
        $this->connection = $database->getConnection();
    }

    public function createOrder(int $userId, array $payload, array $cartItems): int
    {
        $totalAmount = array_reduce(
            $cartItems,
            static fn ($carry, $item) => $carry + ((float) $item['price'] * (int) $item['quantity']),
            0.0
        );

        $query = 'INSERT INTO orders (user_id, total_amount, status, shipping_address, phone, payment_method, created_at) VALUES (:user_id, :total_amount, :status, :shipping_address, :phone, :payment_method, NOW())';
        $statement = $this->connection->prepare($query);
        $statement->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $statement->bindValue(':total_amount', $totalAmount, PDO::PARAM_STR);
        $statement->bindValue(':status', 'Pending', PDO::PARAM_STR);
        $statement->bindValue(':shipping_address', trim($payload['shipping_address'] ?? ''), PDO::PARAM_STR);
        $statement->bindValue(':phone', trim($payload['phone'] ?? ''), PDO::PARAM_STR);
        $statement->bindValue(':payment_method', trim($payload['payment_method'] ?? ''), PDO::PARAM_STR);

        try {
            $this->connection->beginTransaction();
            $statement->execute();
            $orderId = (int) $this->connection->lastInsertId();

            foreach ($cartItems as $item) {
                $itemQuery = 'INSERT INTO order_items (order_id, book_id, quantity, price) VALUES (:order_id, :book_id, :quantity, :price)';
                $itemStatement = $this->connection->prepare($itemQuery);
                $itemStatement->bindValue(':order_id', $orderId, PDO::PARAM_INT);
                $itemStatement->bindValue(':book_id', (int) $item['book_id'], PDO::PARAM_INT);
                $itemStatement->bindValue(':quantity', (int) $item['quantity'], PDO::PARAM_INT);
                $itemStatement->bindValue(':price', (float) $item['price'], PDO::PARAM_STR);
                $itemStatement->execute();
            }

            $this->connection->commit();

            return $orderId;
        } catch (PDOException $exception) {
            $this->connection->rollBack();
            throw $exception;
        }
    }

    public function getOrdersByUser(int $userId): array
    {
        $query = 'SELECT id, total_amount, status, created_at FROM orders WHERE user_id = :user_id ORDER BY created_at DESC';
        $statement = $this->connection->prepare($query);
        $statement->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetchAll();
    }

    public function getOrderByUser(int $userId, int $orderId): array|false
    {
        $query = 'SELECT id, total_amount, status, shipping_address, phone, payment_method, created_at FROM orders WHERE id = :order_id AND user_id = :user_id LIMIT 1';
        $statement = $this->connection->prepare($query);
        $statement->bindValue(':order_id', $orderId, PDO::PARAM_INT);
        $statement->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetch();
    }

    public function getOrderItems(int $orderId): array
    {
        $query = <<<SQL
SELECT oi.book_id,
       oi.quantity,
       oi.price,
       (oi.quantity * oi.price) AS subtotal,
       b.title,
       b.cover_image
FROM order_items oi
LEFT JOIN books b ON b.id = oi.book_id
WHERE oi.order_id = :order_id
SQL;
        $statement = $this->connection->prepare($query);
        $statement->bindValue(':order_id', $orderId, PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetchAll();
    }
}
