<?php

namespace App\Services\ContentGeneration\Claude;

use App\Services\ContentGeneration\Contracts\TextGenerator;

class ClaudeTextGenerator implements TextGenerator
{

    final public function generate(string $description): string
    {
        return "Claude generated text based on: " . $description;
    }
}