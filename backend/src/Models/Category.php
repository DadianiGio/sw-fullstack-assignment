<?php

declare(strict_types=1);

namespace App\Model;

/** Represents a product category (all / clothes / tech). */
class Category
{
    public function __construct(
        private string $name
    ) {
    }

    public function getName(): string { return $this->name; }

    public function toArray(): array
    {
        return [
            'name'       => $this->name,
            '__typename' => 'Category',
        ];
    }
}