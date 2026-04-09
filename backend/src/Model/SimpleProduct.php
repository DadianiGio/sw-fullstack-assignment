<?php

declare(strict_types=1);

namespace App\Model;

/**
 * Represents products with text-only attributes (e.g. clothing sizes).
 */
class SimpleProduct extends AbstractProduct
{
    public function getType(): string
    {
        return 'simple';
    }
}