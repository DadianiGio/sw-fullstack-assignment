<?php

declare(strict_types=1);

namespace App\GraphQL\Schema;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

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