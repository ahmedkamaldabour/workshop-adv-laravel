<?php

namespace App\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
readonly class TripStrategy
{
    public function __construct(
        public string $type
    )
    {
    }
}