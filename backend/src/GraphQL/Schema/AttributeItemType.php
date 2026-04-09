<?php

declare(strict_types=1);

namespace App\GraphQL\Schema;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

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