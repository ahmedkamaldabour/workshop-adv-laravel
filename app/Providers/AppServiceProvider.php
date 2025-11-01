<?php

namespace App\Providers;

use App\Services\Trip\TripStrategyDiscovery;
use App\Services\Trip\TripStrategyResolver;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\ServiceProvider;
use function app_path;
use const DIRECTORY_SEPARATOR;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
    }
}
