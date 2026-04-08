<?php

declare(strict_types=1);

namespace App\GraphQL\Schema;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

/** GraphQL type for Currency (nested inside Price). */
class CurrencyType extends ObjectType
{
    public function __construct()
    {
        parent::__construct([
            'name'   => 'Currency',
            'fields' => [
                'label'  => Type::string(),
                'symbol' => Type::string(),
            ],
        ]);
    }
}

/** GraphQL type for Price. */
class PriceType extends ObjectType
{
    public function __construct(CurrencyType $currencyType)
    {
        parent::__construct([
            'name'   => 'Price',
            'fields' => [
                'amount'   => Type::float(),
                'currency' => $currencyType,
            ],
        ]);
    }
}

/** GraphQL type for Product — attributes resolved via AttributeSetType. */
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
                // Attributes are their own type resolved independently
                'attributes'  => Type::listOf($attributeSetType),
                'prices'      => Type::listOf($priceType),
            ],
        ]);
    }
}