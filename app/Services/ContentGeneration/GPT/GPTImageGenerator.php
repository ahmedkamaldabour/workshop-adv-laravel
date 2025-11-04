<?php

namespace App\Services\ContentGeneration\GPT;

use App\Services\ContentGeneration\Contracts\ImageGenerator;

class GPTImageGenerator implements ImageGenerator
{

    public function generate(string $prompt): string
    {
        return "GPT generated image based on: " . $prompt;
    }
}