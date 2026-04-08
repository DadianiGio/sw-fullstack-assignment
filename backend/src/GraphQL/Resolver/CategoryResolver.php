<?php

declare(strict_types=1);

namespace App\GraphQL\Resolver;

use App\Database\Connection;
use App\Model\Category;

/** Fetches category data from the database. */
class CategoryResolver
{
    public function resolve(): array
    {
        $pdo  = Connection::getInstance();
        $stmt = $pdo->query('SELECT name FROM categories ORDER BY id ASC');
        $rows = $stmt->fetchAll();

        return array_map(
            fn(array $row) => (new Category($row['name']))->toArray(),
            $rows
        );
    }
}