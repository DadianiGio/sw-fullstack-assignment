<?php

declare(strict_types=1);

namespace App\Database;

use PDO;
use PDOException;

/**
 * Singleton database connection using PDO.
 * Ensures only one connection exists throughout the app lifecycle.
 */
class Connection
{
    private static ?PDO $instance = null;

    // Prevent direct instantiation
    private function __construct()
    {
    }

    /**
     * Returns the single PDO instance, creating it if needed.
     */
    public static function getInstance(): PDO
    {
        if (self::$instance === null) {
            $host     = $_ENV['DB_HOST']     ?? 'localhost';
            $port     = $_ENV['DB_PORT']     ?? '3306';
            $database = $_ENV['DB_DATABASE'] ?? 'scandiweb_shop';
            $username = $_ENV['DB_USERNAME'] ?? 'root';
            $password = $_ENV['DB_PASSWORD'] ?? '';

            try {
                self::$instance = new PDO(
                    "mysql:host={$host};port={$port};dbname={$database};charset=utf8mb4",
                    $username,
                    $password,
                    [
                        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_EMULATE_PREPARES   => false,
                    ]
                );
            } catch (PDOException $e) {
                // In production you'd log this; for now surface the message
                throw new \RuntimeException('Database connection failed: ' . $e->getMessage());
            }
        }

        return self::$instance;
    }
}