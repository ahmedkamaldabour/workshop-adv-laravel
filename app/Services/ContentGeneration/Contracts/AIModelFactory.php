<?php

namespace App\Services\ContentGeneration\Contracts;

interface AIModelFactory
{
    public function createTextGenerator(): TextGenerator;

    public function createImageGenerator(): ImageGenerator;
}