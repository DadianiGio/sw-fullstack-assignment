<?php

declare(strict_types=1);

namespace App\GraphQL\Schema;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

class ProductType extends ObjectType
{
    public function __construct(AttributeSetType $attributeSetType, PriceType $priceType)
    {
        parent::__construct([
            'name'   => 'Product',
            'fields' => [
                'id'          => Type::string(),
                'name'        => Type::string(),
                'inStock'     => Type::boolean(),
                'gallery'     => Type::listOf(Type::string()),
                'description' => Type::string(),
                'category'    => Type::string(),
                'brand'       => Type::string(),
                'attributes'  => Type::listOf($attributeSetType),
                'prices'      => Type::listOf($priceType),
            ],
        ]);
    }
}