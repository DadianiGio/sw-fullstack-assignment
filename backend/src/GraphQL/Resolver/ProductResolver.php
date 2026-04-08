<?php

declare(strict_types=1);

namespace App\GraphQL\Resolver;

use App\Database\Connection;
use App\Model\SimpleProduct;
use App\Model\ConfigurableProduct;

/**
 * Resolves product data, instantiating the correct product subclass.
 * SimpleProduct  → clothes (text attributes only)
 * ConfigurableProduct → tech (swatch / mixed attributes)
 */
class ProductResolver
{
    /** Categories that map to ConfigurableProduct */
    private const CONFIGURABLE_CATEGORIES = ['tech'];

    private AttributeResolver $attributeResolver;

    public function __construct()
    {
        $this->attributeResolver = new AttributeResolver();
    }

    /** Return all products, optionally filtered by category. */
    public function resolve(?string $category = null): array
    {
        $pdo = Connection::getInstance();

        if ($category && $category !== 'all') {
            $stmt = $pdo->prepare('SELECT * FROM products WHERE category = :cat ORDER BY id ASC');
            $stmt->execute([':cat' => $category]);
        } else {
            $stmt = $pdo->query('SELECT * FROM products ORDER BY id ASC');
        }

        $rows = $stmt->fetchAll();
        return array_map(fn($row) => $this->buildProduct($row)->toArray(), $rows);
    }

    /** Return a single product by ID. */
    public function resolveById(string $id): ?array
    {
        $pdo  = Connection::getInstance();
        $stmt = $pdo->prepare('SELECT * FROM products WHERE id = :id');
        $stmt->execute([':id' => $id]);
        $row  = $stmt->fetch();

        if (!$row) {
            return null;
        }

        return $this->buildProduct($row)->toArray();
    }

    /** Instantiate the right product subclass and attach prices + attributes. */
    private function buildProduct(array $row): SimpleProduct|ConfigurableProduct
    {
        $gallery    = json_decode($row['gallery'], true) ?? [];
        $attributes = $this->attributeResolver->resolveForProduct($row['id']);
        $prices     = $this->resolvePrices($row['id']);

        $args = [
            $row['id'],
            $row['name'],
            (bool) $row['in_stock'],
            $gallery,
            $row['description'],
            $row['category'],
            $attributes,
            $prices,
            $row['brand'],
        ];

        // Choose subclass based on category — no if/switch on type differences
        if (in_array($row['category'], self::CONFIGURABLE_CATEGORIES, true)) {
            return new ConfigurableProduct(...$args);
        }

        return new SimpleProduct(...$args);
    }

    private function resolvePrices(string $productId): array
    {
        $pdo  = Connection::getInstance();
        $stmt = $pdo->prepare('SELECT * FROM prices WHERE product_id = :pid');
        $stmt->execute([':pid' => $productId]);
        $rows = $stmt->fetchAll();

        return array_map(fn($r) => [
            'amount'     => (float) $r['amount'],
            'currency'   => [
                'label'      => $r['currency_label'],
                'symbol'     => $r['currency_symbol'],
                '__typename' => 'Currency',
            ],
            '__typename' => 'Price',
        ], $rows);
    }
}