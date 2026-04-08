<?php

declare(strict_types=1);

namespace App\GraphQL\Mutation;

use App\Database\Connection;

/** Handles the placeOrder GraphQL mutation — inserts order into DB. */
class OrderMutation
{
    /**
     * @param array $items  Each item: { productId, quantity, selectedAttributes }
     * @return array        Result with orderId and success flag
     */
    public function placeOrder(array $items): array
    {
        $pdo = Connection::getInstance();
        $pdo->beginTransaction();

        try {
            // Create the order record
            $pdo->exec(
                "INSERT INTO orders (created_at) VALUES (NOW())"
            );
            $orderId = (int) $pdo->lastInsertId();

            // Insert each order item
            $itemStmt = $pdo->prepare(
                'INSERT INTO order_items (order_id, product_id, quantity, selected_attributes)
                 VALUES (:order_id, :product_id, :quantity, :selected_attributes)'
            );

            foreach ($items as $item) {
                $itemStmt->execute([
                    ':order_id'            => $orderId,
                    ':product_id'          => $item['productId'],
                    ':quantity'            => $item['quantity'],
                    ':selected_attributes' => json_encode($item['selectedAttributes'] ?? []),
                ]);
            }

            $pdo->commit();

            return ['orderId' => $orderId, 'success' => true];
        } catch (\Throwable $e) {
            $pdo->rollBack();
            throw $e;
        }
    }
}