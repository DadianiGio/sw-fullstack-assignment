<?php

declare(strict_types=1);

namespace App\GraphQL\Resolver;

use App\Database\Connection;
use App\Model\TextAttribute;
use App\Model\SwatchAttribute;

/**
 * Resolves attributes for a given product.
 * Polymorphism: instantiates TextAttribute or SwatchAttribute based on type —
 * no switch/if required for the core type behaviour differences.
 */
class AttributeResolver
{
    /**
     * Map of type string → class name.
     * Adding a new attribute type = adding one entry here, no if/switch needed.
     */
    private const TYPE_MAP = [
        'text'   => TextAttribute::class,
        'swatch' => SwatchAttribute::class,
    ];

    public function resolveForProduct(string $productId): array
    {
        $pdo = Connection::getInstance();

        // Fetch attribute sets for this product
        $stmt = $pdo->prepare(
            'SELECT * FROM attribute_sets WHERE product_id = :pid ORDER BY id ASC'
        );
        $stmt->execute([':pid' => $productId]);
        $sets = $stmt->fetchAll();

        $result = [];

        foreach ($sets as $set) {
            // Fetch individual items for this attribute set
            $itemStmt = $pdo->prepare(
                'SELECT * FROM attribute_items WHERE attribute_set_id = :sid ORDER BY id ASC'
            );
            $itemStmt->execute([':sid' => $set['id']]);
            $rawItems = $itemStmt->fetchAll();

            $items = array_map(fn($i) => [
                'id'           => $i['item_id'],
                'displayValue' => $i['display_value'],
                'value'        => $i['value'],
                '__typename'   => 'Attribute',
            ], $rawItems);

            // Resolve to the correct attribute class via type map
            $class = self::TYPE_MAP[$set['type']] ?? TextAttribute::class;

            /** @var \App\Model\AbstractAttribute $attr */
            $attr = new $class($set['attribute_id'], $set['name'], $set['type'], $items);
            $result[] = $attr->toArray();
        }

        return $result;
    }
}