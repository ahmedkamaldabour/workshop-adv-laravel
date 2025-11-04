<?php

namespace App\Services\ContentGeneration\Claude;

use App\Services\ContentGeneration\Contracts\AIModelFactory;
use App\Services\ContentGeneration\Contracts\TextGenerator;
use App\Services\ContentGeneration\Contracts\ImageGenerator;

class ClaudeFactory implements AIModelFactory
{

    final public function createTextGenerator(): TextGenerator
    {
        return new ClaudeTextGenerator();
    }

    final public function createImageGenerator(): ImageGenerator
    {
        return new ClaudeImageGenerator();
    }
}