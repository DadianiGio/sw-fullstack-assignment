<?php

declare(strict_types=1);

namespace App\Model;

/** Handles colour-swatch attributes rendered as colour squares. */
class SwatchAttribute extends AbstractAttribute
{
    public function getDisplayType(): string
    {
        return 'swatch';
    }
}