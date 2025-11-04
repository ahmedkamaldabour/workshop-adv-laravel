<?php

namespace App\Services\ContentGeneration\GPT;

use App\Services\ContentGeneration\Contracts\AIModelFactory;
use App\Services\ContentGeneration\Contracts\TextGenerator;
use App\Services\ContentGeneration\Contracts\ImageGenerator;


class GPTFactory implements AIModelFactory
{

    final public function createTextGenerator(): TextGenerator
    {
        return new GPTTextGenerator();
    }

    final public function createImageGenerator(): ImageGenerator
    {
        return new GPTImageGenerator();
    }
}