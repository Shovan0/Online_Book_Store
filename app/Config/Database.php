<?php

declare(strict_types=1);

namespace App\Config;

use PDO;
use PDOException;

/**
 * Database connection class for Supabase PostgreSQL.
 */
class Database
{
    private PDO $connection;

    /**
     * Initialize the database connection.
     *
     * @throws PDOException When connection fails.
     */
    public function __construct()
    {
        $this->connect();
    }

    /**
     * Create and configure the PDO connection.
     *
     * @return void
     *
     * @throws PDOException When connection fails.
     */
    private function connect(): void
    {
        $host = $_ENV['DB_HOST'] ?? '';
        $port = $_ENV['DB_PORT'] ?? '5432';
        $database = $_ENV['DB_DATABASE'] ?? '';
        $username = $_ENV['DB_USERNAME'] ?? '';
        $password = $_ENV['DB_PASSWORD'] ?? '';

        $dsn = sprintf('pgsql:host=%s;port=%s;dbname=%s;sslmode=require', $host, $port, $database);
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::ATTR_PERSISTENT => false,
        ];

        try {
            $this->connection = new PDO($dsn, $username, $password, $options);
            $this->connection->exec("SET NAMES 'utf8'");
        } catch (PDOException $exception) {
            throw new PDOException(
                'Database connection failed: ' . $exception->getMessage(),
                (int) $exception->getCode(),
                $exception
            );
        }
    }

    /**
     * Return the PDO connection instance.
     */
    public function getConnection(): PDO
    {
        return $this->connection;
    }
}
