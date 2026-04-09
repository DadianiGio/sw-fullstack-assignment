<?php

declare(strict_types=1);

namespace App\Model;

/**
 * Represents products with swatch/configurable attributes (e.g. colour).
 */
class ConfigurableProduct extends AbstractProduct
{
    public function getType(): string
    {
        return 'configurable';
    }
}