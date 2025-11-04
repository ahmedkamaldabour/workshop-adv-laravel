<?php

namespace App\Services\ContentGeneration;

use Exception;

class AIService
{
    /**
     * @throws Exception
     */
    final public function generateText(string $model, string $prompt): string
    {
        $factory = AIModelFactoryResolver::resolve($model);
        return $factory->createTextGenerator()->generate($prompt);
    }

    /**
     * @throws Exception
     */
    final public function generateImage(string $model, string $description): string
    {
        $factory = AIModelFactoryResolver::resolve($model);
        return $factory->createImageGenerator()->generate($description);
    }
}