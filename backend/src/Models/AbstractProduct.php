<?php

declare(strict_types=1);

namespace App\Model;

/**
 * Base product. All product types extend this.
 * Polymorphism: subclasses define type-specific behaviour.
 */
abstract class AbstractProduct
{
    public function __construct(
        protected string $id,
        protected string $name,
        protected bool   $inStock,
        protected array  $gallery,
        protected string $description,
        protected string $category,
        protected array  $attributes,
        protected array  $prices,
        protected string $brand,
    ) {
    }

    // Subclasses declare what "type" they are
    abstract public function getType(): string;

    /*Getters */

    public function getId(): string        { return $this->id; }
    public function getName(): string      { return $this->name; }
    public function isInStock(): bool      { return $this->inStock; }
    public function getGallery(): array    { return $this->gallery; }
    public function getDescription(): string { return $this->description; }
    public function getCategory(): string  { return $this->category; }
    public function getAttributes(): array { return $this->attributes; }
    public function getPrices(): array     { return $this->prices; }
    public function getBrand(): string     { return $this->brand; }

    /**
     * Serialises the product to an array GraphQL resolvers can return.
     */
    public function toArray(): array
    {
        return [
            'id'          => $this->id,
            'name'        => $this->name,
            'inStock'     => $this->inStock,
            'gallery'     => $this->gallery,
            'description' => $this->description,
            'category'    => $this->category,
            'attributes'  => $this->attributes,
            'prices'      => $this->prices,
            'brand'       => $this->brand,
            '__typename'  => 'Product',
        ];
    }
}