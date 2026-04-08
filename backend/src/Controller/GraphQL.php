<?php

declare(strict_types=1);

namespace App\Controller;

use App\GraphQL\Mutation\OrderMutation;
use App\GraphQL\Resolver\CategoryResolver;
use App\GraphQL\Resolver\ProductResolver;
use App\GraphQL\Schema\AttributeItemType;
use App\GraphQL\Schema\AttributeSetType;
use App\GraphQL\Schema\CategoryType;
use App\GraphQL\Schema\CurrencyType;
use App\GraphQL\Schema\PriceType;
use App\GraphQL\Schema\ProductType;
use GraphQL\GraphQL as GraphQLBase;
use GraphQL\Type\Definition\InputObjectType;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Schema;
use GraphQL\Type\SchemaConfig;
use RuntimeException;
use Throwable;

class GraphQL
{
    public static function handle(): string
    {
        try {
            // Instantiate all types 
            $categoryType  = new CategoryType();
            $attributeItem = new AttributeItemType();
            $attributeSet  = new AttributeSetType($attributeItem);
            $currency      = new CurrencyType();
            $price         = new PriceType($currency);
            $productType   = new ProductType($attributeSet, $price);

            //Resolvers & mutations
            $categoryResolver = new CategoryResolver();
            $productResolver  = new ProductResolver();
            $orderMutation    = new OrderMutation();

            //Input types for placeOrder mutation
            $selectedAttributeInput = new InputObjectType([
                'name'   => 'SelectedAttributeInput',
                'fields' => [
                    'attributeId' => Type::string(),
                    'itemId'      => Type::string(),
                ],
            ]);

            $orderItemInput = new InputObjectType([
                'name'   => 'OrderItemInput',
                'fields' => [
                    'productId'          => Type::nonNull(Type::string()),
                    'quantity'           => Type::nonNull(Type::int()),
                    'selectedAttributes' => Type::listOf($selectedAttributeInput),
                ],
            ]);

            //Order result type
            $orderResultType = new ObjectType([
                'name'   => 'OrderResult',
                'fields' => [
                    'orderId' => Type::int(),
                    'success' => Type::boolean(),
                ],
            ]);

            //Query type 
            $queryType = new ObjectType([
                'name'   => 'Query',
                'fields' => [
                    'categories' => [
                        'type'    => Type::listOf($categoryType),
                        'resolve' => fn () => $categoryResolver->resolve(),
                    ],
                    'products' => [
                        'type' => Type::listOf($productType),
                        'args' => [
                            'category' => ['type' => Type::string()],
                        ],
                        'resolve' => fn ($root, array $args) =>
                            $productResolver->resolve($args['category'] ?? null),
                    ],
                    'product' => [
                        'type' => $productType,
                        'args' => [
                            'id' => ['type' => Type::nonNull(Type::string())],
                        ],
                        'resolve' => fn ($root, array $args) =>
                            $productResolver->resolveById($args['id']),
                    ],
                ],
            ]);

            // Mutation type
            $mutationType = new ObjectType([
                'name'   => 'Mutation',
                'fields' => [
                    'placeOrder' => [
                        'type' => $orderResultType,
                        'args' => [
                            'items' => Type::nonNull(
                                Type::listOf(Type::nonNull($orderItemInput))
                            ),
                        ],
                        'resolve' => fn ($root, array $args) =>
                            $orderMutation->placeOrder($args['items']),
                    ],
                ],
            ]);

            //  Build schema
            $schema = new Schema(
                (new SchemaConfig())
                    ->setQuery($queryType)
                    ->setMutation($mutationType)
            );

            // Read & parse input
            $rawInput = file_get_contents('php://input');
            if ($rawInput === false) {
                throw new RuntimeException('Failed to get php://input');
            }

            $input          = json_decode($rawInput, true);
            $query          = $input['query'];
            $variableValues = $input['variables'] ?? null;

            
            $result = GraphQLBase::executeQuery(
                $schema,
                $query,
                null,   
                null,
                $variableValues
            );

            $output = $result->toArray();

        } catch (Throwable $e) {
            $output = [
                'error' => [
                    'message' => $e->getMessage(),
                ],
            ];
        }

        header('Content-Type: application/json; charset=UTF-8');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization');

        return json_encode($output);
    }
}