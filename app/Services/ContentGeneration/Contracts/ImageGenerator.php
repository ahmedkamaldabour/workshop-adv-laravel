<?php

namespace App\Services\ContentGeneration\Contracts;

interface ImageGenerator
{
    public function generate(string $prompt): string;

}