<?php

declare(strict_types=1);

namespace App\GraphQL\Schema;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

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