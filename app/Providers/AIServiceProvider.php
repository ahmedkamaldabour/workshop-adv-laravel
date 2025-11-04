<?php

namespace App\Providers;

use App\Services\ContentGeneration\AIService;
use Illuminate\Support\ServiceProvider;

class AIServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(AIService::class, static function ($app) {
            return new AIService();
        });
    }
}
