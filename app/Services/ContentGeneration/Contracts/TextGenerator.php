<?php

namespace App\Services\ContentGeneration\Contracts;

interface TextGenerator
{
    public function generate(string $description): string;

}