<?php

declare(strict_types=1);

namespace App\GraphQL\Schema;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

/** GraphQL type for a single attribute option (e.g. "Large"). */
class AttributeItemType extends ObjectType
{
    public function __construct()
    {
        parent::__construct([
            'name'   => 'Attribute',
            'fields' => [
                'id'           => Type::string(),
                'displayValue' => Type::string(),
                'value'        => Type::string(),
            ],
        ]);
    }
}

/** GraphQL type for an attribute set (e.g. "Size: S, M, L, XL"). */
class AttributeSetType extends ObjectType
{
    public function __construct(AttributeItemType $itemType)
    {
        parent::__construct([
            'name'   => 'AttributeSet',
            'fields' => [
                'id'    => Type::string(),
                'name'  => Type::string(),
                'type'  => Type::string(),
                'items' => Type::listOf($itemType),
            ],
        ]);
    }
}