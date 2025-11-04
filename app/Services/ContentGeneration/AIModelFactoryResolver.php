<?php

namespace App\Services\ContentGeneration;

use App\Services\ContentGeneration\Claude\ClaudeFactory;
use App\Services\ContentGeneration\GPT\GPTFactory;
use Exception;

class AIModelFactoryResolver
{
    /**
     * @throws Exception
     */
    public static function resolve(string $model): ClaudeFactory|GPTFactory
    {
        return match (strtolower($model)) {
            'gpt' => new GPTFactory(),
            'claude' => new ClaudeFactory(),
            default => throw new Exception("Unsupported AI model: {$model}"),
        };
    }
}