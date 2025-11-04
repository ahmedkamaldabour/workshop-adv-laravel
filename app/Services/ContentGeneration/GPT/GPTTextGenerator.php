<?php

namespace App\Services\ContentGeneration\GPT;

use App\Services\ContentGeneration\Contracts\TextGenerator;

class GPTTextGenerator implements TextGenerator
{

    final public function generate(string $description): string
    {
        return "GPT generated text based on: " . $description;
    }
}