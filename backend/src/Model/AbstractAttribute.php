<?php

declare(strict_types=1);

namespace App\Model;

/**
 * Base attribute. TextAttribute and SwatchAttribute extend this.
 */
abstract class AbstractAttribute
{
    public function __construct(
        protected string $id,
        protected string $name,
        protected string $type,
        protected array  $items,
    ) {
    }

    abstract public function getDisplayType(): string;

    public function getId(): string   { return $this->id; }
    public function getName(): string { return $this->name; }
    public function getType(): string { return $this->type; }
    public function getItems(): array { return $this->items; }

    public function toArray(): array
    {
        return [
            'id'          => $this->id,
            'name'        => $this->name,
            'type'        => $this->type,
            'items'       => $this->items,
            '__typename'  => 'AttributeSet',
        ];
    }
}