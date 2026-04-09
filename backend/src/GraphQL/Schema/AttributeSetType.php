<?php

declare(strict_types=1);

namespace App\GraphQL\Schema;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

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