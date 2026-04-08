<?php

declare(strict_types=1);

namespace App\Model;

/** Handles text-based attributes like sizes (S, M, L, XL). */
class TextAttribute extends AbstractAttribute
{
    public function getDisplayType(): string
    {
        return 'text';
    }
}