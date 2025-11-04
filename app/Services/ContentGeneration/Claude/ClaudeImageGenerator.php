<?php

namespace App\Services\ContentGeneration\Claude;

use App\Services\ContentGeneration\Contracts\ImageGenerator;

class ClaudeImageGenerator implements ImageGenerator
{

    final public function generate(string $prompt): string
    {
        return "Claude generated image based on: " . $prompt;
    }
}