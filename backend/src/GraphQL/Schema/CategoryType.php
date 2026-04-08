<?php

declare(strict_types=1);

namespace App\GraphQL\Schema;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

/** GraphQL type definition for Category. */
class CategoryType extends ObjectType
{
    public function __construct()
    {
        parent::__construct([
            'name'   => 'Category',
            'fields' => [
                'name' => Type::string(),
            ],
        ]);
    }
}